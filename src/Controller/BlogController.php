<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use App\Entity\Blog;
use App\Entity\Blogtype;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(): Response
    {
        $blogs = $this->getDoctrine()->getRepository(Blog::class)->findAll();
        $blogtypes = $this->getDoctrine()->getRepository(Blogtype::class)->findAll();
        foreach($blogtypes as $blogtype) {
            $blogtype->blogs = $this->getDoctrine()->getRepository(Blog::class)->findAllWithType($blogtype->getId());
        }
        foreach($blogs as $blog) {
            $blog->type = $this->getDoctrine()->getRepository(Blogtype::class)->find($blog->getTypeId());
        }
        return $this->render('pages/blog/index.html.twig', [
            'blogtypes' => $blogtypes,
            'blogs' => $blogs
        ]);
    }

    /**
     * @Route("/blog/detail/{id}", name="blog_detail")
     */
    public function detail($id): Response
    {
        $blog = $this->getDoctrine()->getRepository(Blog::class)->find($id);
        $blog->type = $this->getDoctrine()->getRepository(Blogtype::class)->find($blog->getTypeId());
        return $this->render('pages/blog/detail.html.twig', [
            'blog' => $blog
        ]);
    }

    /**
     * @Route("/admin/blog", name="admin_blog")
     */
    public function admin_index(): Response
    {
        $blogs = $this->getDoctrine()->getRepository(Blog::class)->findAll();
        foreach($blogs as $blog) {
            $blogtype = $this->getDoctrine()->getRepository(Blogtype::class)->find($blog->getTypeId());
            $blog->type = $blogtype->getName();
        }
        return $this->render('pages/admin/blog/index.html.twig', [
            'blogs' => $blogs
        ]);
    }

    /**
     * @Route("/admin/blog/create", name="admin_blog_create")
     */
    public function admin_create(): Response
    {
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
        $doct = $this->getDoctrine()->getManager();
        $blog = $doct->getRepository(Blog::class)->find($id);
        $doct->remove($blog);
        $doct->flush();
        return $this->redirectToRoute('admin_blog', [
            'id' => $blog->getId()
        ]);
    }

    /**
     * @Route("/admin/blogtype", name="admin_blogtype")
     */
    public function admin_type_index(): Response
    {
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
        return $this->render('pages/admin/blogtype/create.html.twig', [
        ]);
    }

    /**
     * @Route("/admin/blogtype/store", name="admin_blogtype_store")
     */
    public function admin_type_store(Request $request, ValidatorInterface $validator): Response
    {
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
