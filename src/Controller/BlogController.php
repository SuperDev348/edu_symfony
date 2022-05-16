<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Blog;
use App\Entity\Blogtype;
use App\Entity\BlogComment;
use App\Entity\BlogCommentLike;
use App\Entity\User;
use \DateTime;

class BlogController extends AbstractController
{
    protected $session;
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    private function isAuth() {
        if(is_null($this->session->get('user'))){
            return false;
        }
        $user = $this->getDoctrine()->getRepository(User::class)->find($this->session->get('user')->getId());
        if ($user->getBan()) {
            $this->session->clear();
            return false;
        }
        return true;
    }

    private function isAdmin() {
        if(is_null($this->session->get('user'))||$this->session->get('user')->getType()!="admin"){
            return false;
        }
        $user = $this->getDoctrine()->getRepository(User::class)->find($this->session->get('user')->getId());
        if ($user->getBan()) {
            $this->session->clear();
            return false;
        }
        return true;
    }
    /**
     * @Route("/blog", name="blog")
     */
    public function index(): Response
    {
        $blogs = $this->getDoctrine()->getRepository(Blog::class)->findAll();
        $top_blogs = array_slice($blogs, 0, 5);
        $blogtypes = $this->getDoctrine()->getRepository(Blogtype::class)->findAll();
        foreach($blogtypes as $blogtype) {
            $blogtype->blogs = $this->getDoctrine()->getRepository(Blog::class)->findAllWithType($blogtype->getId());
        }
        foreach($blogs as $blog) {
            $blog->type = $this->getDoctrine()->getRepository(Blogtype::class)->find($blog->getTypeId());
            $blog->user = $this->getDoctrine()->getRepository(User::class)->find($blog->getUserId());
        }
        return $this->render('pages/blog/index.html.twig', [
            'blogtypes' => $blogtypes,
            'blogs' => $blogs,
            'top_blogs' => $top_blogs
        ]);
    }

    /**
     * @Route("/blog/detail/{id}", name="blog_detail")
     */
    public function detail($id): Response
    {
        $blog = $this->getDoctrine()->getRepository(Blog::class)->find($id);
        $blog->type = $this->getDoctrine()->getRepository(Blogtype::class)->find($blog->getTypeId());
        $blog->user = $this->getDoctrine()->getRepository(User::class)->find($blog->getUserId());
        $relate_blogs = $this->relate_blogs($id);
        $comments_all = $this->getDoctrine()->getRepository(BlogComment::class)->findAllWithBlogId($id);
        $comments = $this->getDoctrine()->getRepository(BlogComment::class)->findWithBlogId($id, 0);
        if(is_null($this->session->get('user'))){
            foreach ($comments as $comment) {
                $reply = $this->getDoctrine()->getRepository(BlogComment::class)->findWithBlogId($id, $comment->getId());
                foreach ($reply as $r) {
                    $r->user = $this->getDoctrine()->getRepository(User::class)->find($r->getUserId());
                }
                $comment->user = $this->getDoctrine()->getRepository(User::class)->find($comment->getUserId());
                $comment->reply = $reply;
            }
        }
        else {
            $user_id = $this->session->get('user')->getId();
            $likes = $this->getDoctrine()->getRepository(BlogCommentLike::class)->findWithUser($user_id);
            $like_ids = [];
            foreach ($likes as $like) {
                array_push($like_ids, $like->getBlogcommentId());
            }
            foreach ($comments as $comment) {
                $reply = $this->getDoctrine()->getRepository(BlogComment::class)->findWithBlogId($id, $comment->getId());
                foreach ($reply as $r) {
                    $r->user = $this->getDoctrine()->getRepository(User::class)->find($r->getUserId());
                    if (in_array($r->getId(), $like_ids))
                        $r->attached = true;
                    else
                        $r->attached = false;
                }
                $comment->user = $this->getDoctrine()->getRepository(User::class)->find($comment->getUserId());
                $comment->reply = $reply;
                if (in_array($comment->getId(), $like_ids))
                    $comment->attached = true;
                else
                    $comment->attached = false;
            }
        }

        return $this->render('pages/blog/detail.html.twig', [
            'blog' => $blog,
            'comment_count' => count($comments_all),
            'relate_blogs' => $relate_blogs,
            'comments' => $comments
        ]);
    }

    private function relate_blogs($id) {
        $blogs = $this->getDoctrine()->getRepository(Blog::class)->findAll();
        foreach ($blogs as $index => $blog) {
            if ($blog->getId() == $id) {
                unset($blogs[$index]); 
                break;
            }
        }
        $blogs = array_slice($blogs, 0, 4);
        foreach ($blogs as $blog) {
            $blog->type = $this->getDoctrine()->getRepository(Blogtype::class)->find($blog->getTypeId());
        }
        return $blogs;
    } 

    /**
     * @Route("/admin/blog", name="admin_blog")
     */
    public function admin_index(): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $blogs = $this->getDoctrine()->getRepository(Blog::class)->findAll();
        foreach($blogs as $blog) {
            $blogtype = $this->getDoctrine()->getRepository(Blogtype::class)->find($blog->getTypeId());
            $blog->type = $blogtype->getName();
            $blog->user = $this->getDoctrine()->getRepository(User::class)->find($blog->getUserId());
        }
        $types = $this->getDoctrine()->getRepository(Blogtype::class)->findAll();
        return $this->render('pages/admin/blog/index.html.twig', [
            'blogs' => $blogs,
            'types' => $types
        ]);
    }

    /**
     * @Route("/admin/blog/create", name="admin_blog_create")
     */
    public function admin_create(): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $blogs = $this->getDoctrine()->getRepository(Blog::class)->findAll();
        $blogtypes = $this->getDoctrine()->getRepository(Blogtype::class)->findAll();
        return $this->render('pages/admin/blog/create.html.twig', [
            'blogs' => $blogs,
            'blogtypes' => $blogtypes,
        ]);
    }

    /**
     * @Route("/admin/blog/store", name="admin_blog_store")
     */
    public function admin_store(Request $request, ValidatorInterface $validator): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $title = $request->request->get("title");
        $type_id = $request->request->get("type_id");
        $image = $request->request->get("image");
        $discription = $request->request->get("discription");
        $input = [
            'title' => $title,
            'type_id' => $type_id,
            'discription' => $discription,
        ];
        $constraints = new Assert\Collection([
            'title' => [new Assert\NotBlank],
            'type_id' => [new Assert\NotBlank],
            'discription' => [new Assert\NotBlank],
        ]);
        $violations = $validator->validate($input, $constraints);
        if (count($violations) > 0) {
            $accessor = PropertyAccess::createPropertyAccessor();
            $errorMessages = [];
            foreach ($violations as $violation) {
                $accessor->setValue($errorMessages,
                $violation->getPropertyPath(),
                $violation->getMessage());
            }
            $blogs = $this->getDoctrine()->getRepository(Blog::class)->findAll();
            $blogtypes = $this->getDoctrine()->getRepository(Blogtype::class)->findAll();
            return $this->render('pages/admin/blog/create.html.twig', [
                'blogs' => $blogs,
                'blogtypes' => $blogtypes,
                'errors' => $errorMessages,
                'old' => $input
            ]);
        }
        
        $blog = new Blog();
        $title = $request->request->get('title');
        $blog->setTitle($title);
        $type_id = $request->request->get('type_id');
        $blog->setTypeId($type_id);
        $discription = $request->request->get('discription');
        $blog->setDiscription($discription);
        $blog->setUserId($this->session->get('user')->getId());
        $date = new DateTime();
        $blog->setDate($date);
        $image_file = $request->files->get('image');
        if ($image_file) {
            $originalFilename = pathinfo($image_file->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $this->generateRandomString();
            $newFilename = $safeFilename.'.'.$image_file->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $image_file->move(
                    'upload/images/',
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            // updates the 'brochureFilename' property to store the PDF file name
            // instead of its contents
            $blog->setImage('upload/images/'.$newFilename);
        }
        else {
            $errorMessages = ['image' => 'this field is require'];
            $blogs = $this->getDoctrine()->getRepository(Blog::class)->findAll();
            return $this->render('pages/admin/blog/create.html.twig', [
                'blogs' => $blogs,
                'errors' => $errorMessages,
                'old' => $input
            ]);
        }
        
        // save
        $doct = $this->getDoctrine()->getManager();
        $doct->persist($blog);
        $doct->flush();
        return $this->redirectToRoute('admin_blog');
    }

    /**
     * @Route("/admin/blog/edit/{id}", name="admin_blog_edit")
     */
    public function admin_edit($id): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $blog = $this->getDoctrine()->getRepository(Blog::class)->find($id);
        $blogtypes = $this->getDoctrine()->getRepository(Blogtype::class)->findAll();
        return $this->render('pages/admin/blog/edit.html.twig', [
            'blog' => $blog,
            'blogtypes' => $blogtypes,
        ]);
    }

    /**
     * @Route("/admin/blog/update/{id}", name="admin_blog_update")
     */
    public function admin_update($id, Request $request, ValidatorInterface $validator): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $title = $request->request->get("title");
        $type_id = $request->request->get("type_id");
        $image = $request->request->get("image");
        $discription = $request->request->get("discription");
        $input = [
            'title' => $title,
            'type_id' => $type_id,
            'discription' => $discription,
        ];
        $constraints = new Assert\Collection([
            'title' => [new Assert\NotBlank],
            'type_id' => [new Assert\NotBlank],
            'discription' => [new Assert\NotBlank],
        ]);
        $violations = $validator->validate($input, $constraints);
        if (count($violations) > 0) {
            $accessor = PropertyAccess::createPropertyAccessor();
            $errorMessages = [];
            foreach ($violations as $violation) {
                $accessor->setValue($errorMessages,
                $violation->getPropertyPath(),
                $violation->getMessage());
            }
            $blog = $this->getDoctrine()->getRepository(Blog::class)->find($id);
            $blogtypes = $this->getDoctrine()->getRepository(Blogtype::class)->findAll();
            return $this->render('pages/admin/blog/create.html.twig', [
                'blog' => $blog,
                'blogtypes' => $blogtypes,
                'errors' => $errorMessages,
                'old' => $input
            ]);
        }
        
        $doct = $this->getDoctrine()->getManager();
        $blog = $doct->getRepository(Blog::class)->find($id);
        $title = $request->request->get('title');
        $blog->setTitle($title);
        $type_id = $request->request->get('type_id');
        $blog->setTypeId($type_id);
        $discription = $request->request->get('discription');
        $blog->setDiscription($discription);
        $blog->setUserId($this->session->get('user')->getId());
        $date = new DateTime();
        $blog->setDate($date);
        $image_file = $request->files->get('image');
        if ($image_file) {
            $originalFilename = pathinfo($image_file->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $this->generateRandomString();
            $newFilename = $safeFilename.'.'.$image_file->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $image_file->move(
                    'upload/images/',
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            // updates the 'brochureFilename' property to store the PDF file name
            // instead of its contents
            $blog->setImage('upload/images/'.$newFilename);
        }
        else {
            $errorMessages = ['image' => 'this field is require'];
            $blog = $this->getDoctrine()->getRepository(Blog::class)->find($id);
            $blogtypes = $this->getDoctrine()->getRepository(Blogtype::class)->findAll();
            return $this->render('pages/admin/blog/create.html.twig', [
                'blog' => $blog,
                'blogtypes' => $blogtypes,
                'errors' => $errorMessages,
                'old' => $input
            ]);
        }
        
        // update
        $doct->flush();
        return $this->redirectToRoute('admin_blog', [
            'id' => $blog->getId()
        ]);
    }

    /**
     * @Route("/admin/blog/delete/{id}", name="admin_blog_delete")
     */
    public function admin_delete($id): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $doct = $this->getDoctrine()->getManager();
        $blog = $doct->getRepository(Blog::class)->find($id);
        $doct->remove($blog);
        $doct->flush();
        return $this->redirectToRoute('admin_blog', [
            'id' => $blog->getId()
        ]);
    }

    /**
     * @Route("/admin/blog/search", name="admin_blog_search")
     */
    public function admin_search(Request $request): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $type_id = $request->request->get('type_id');
        $filter = [];
        if ($type_id != '0')
            $filter['type_id'] = $type_id;
        
        $doct = $this->getDoctrine()->getManager();
        $blogs = $doct->getRepository(Blog::class)->findWithFilter($filter);
        foreach($blogs as $blog) {
            $blogtype = $this->getDoctrine()->getRepository(Blogtype::class)->find($blog->getTypeId());
            $blog->type = $blogtype->getName();
            $blog->user = $this->getDoctrine()->getRepository(User::class)->find($blog->getUserId());
        }
        $types = $doct->getRepository(Blogtype::class)->findAll();
        return $this->render('pages/admin/blog/index.html.twig', [
            'blogs' => $blogs,
            'filter' => $filter,
            'types' => $types
        ]);
    }

    /**
     * @Route("/admin/blogtype", name="admin_blogtype")
     */
    public function admin_type_index(): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $blogtypes = $this->getDoctrine()->getRepository(BlogType::class)->findAll();
        return $this->render('pages/admin/blogtype/index.html.twig', [
            'blogtypes' => $blogtypes
        ]);
    }

    /**
     * @Route("/admin/blogtype/create", name="admin_blogtype_create")
     */
    public function admin_type_create(): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        return $this->render('pages/admin/blogtype/create.html.twig', [
        ]);
    }

    /**
     * @Route("/admin/blogtype/store", name="admin_blogtype_store")
     */
    public function admin_type_store(Request $request, ValidatorInterface $validator): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $name = $request->request->get("name");
        $input = [
            'name' => $name,
        ];
        $constraints = new Assert\Collection([
            'name' => [new Assert\NotBlank],
        ]);
        $violations = $validator->validate($input, $constraints);
        if (count($violations) > 0) {
            $accessor = PropertyAccess::createPropertyAccessor();
            $errorMessages = [];
            foreach ($violations as $violation) {
                $accessor->setValue($errorMessages,
                $violation->getPropertyPath(),
                $violation->getMessage());
            }
            return $this->render('pages/admin/blogtype/create.html.twig', [
                'errors' => $errorMessages,
                'old' => $input
            ]);
        }
        
        $blogtype = new Blogtype();
        $name = $request->request->get('name');
        $blogtype->setName($name);
        
        // save
        $doct = $this->getDoctrine()->getManager();
        $doct->persist($blogtype);
        $doct->flush();
        return $this->redirectToRoute('admin_blogtype');
    }

    /**
     * @Route("/admin/blogtype/edit/{id}", name="admin_blogtype_edit")
     */
    public function admin_type_edit($id): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $blogtype = $this->getDoctrine()->getRepository(Blogtype::class)->find($id);
        return $this->render('pages/admin/blogtype/edit.html.twig', [
            'blogtype' => $blogtype,
        ]);
    }

    /**
     * @Route("/admin/blogtype/update/{id}", name="admin_blogtype_update")
     */
    public function admin_type_update($id, Request $request, ValidatorInterface $validator): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $name = $request->request->get("name");
        $input = [
            'name' => $name,
        ];
        $constraints = new Assert\Collection([
            'name' => [new Assert\NotBlank],
        ]);
        $violations = $validator->validate($input, $constraints);
        if (count($violations) > 0) {
            $accessor = PropertyAccess::createPropertyAccessor();
            $errorMessages = [];
            foreach ($violations as $violation) {
                $accessor->setValue($errorMessages,
                $violation->getPropertyPath(),
                $violation->getMessage());
            }
            $blogtype = $this->getDoctrine()->getRepository(Blogtype::class)->find($id);
            return $this->render('pages/admin/blogtype/edit.html.twig', [
                'blogtype' => $blogtype,
                'errors' => $errorMessages,
                'old' => $input
            ]);
        }
        
        $doct = $this->getDoctrine()->getManager();
        $blogtype = $doct->getRepository(Blogtype::class)->find($id);
        $name = $request->request->get('name');
        $blogtype->setName($name);
        
        // update
        $doct->flush();
        return $this->redirectToRoute('admin_blogtype', [
            'id' => $blogtype->getId()
        ]);
    }

    /**
     * @Route("/admin/blogtype/delete/{id}", name="admin_blogtype_delete")
     */
    public function admin_type_delete($id): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $doct = $this->getDoctrine()->getManager();
        $blogtype = $doct->getRepository(Blogtype::class)->find($id);
        $doct->remove($blogtype);
        $doct->flush();
        return $this->redirectToRoute('admin_blogtype', [
            'id' => $blogtype->getId()
        ]);
    }

    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
