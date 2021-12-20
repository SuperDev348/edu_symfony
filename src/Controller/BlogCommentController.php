<?php
namespace App\Controller;
      
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use App\Entity\Blog;
use App\Entity\BlogComment;
use App\Entity\User;
use \DateTime;

class BlogCommentController extends AbstractController
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
     * @Route("/blogcomment/store", name="blog_comment_store")
     */
    public function store(Request $request, ValidatorInterface $validator): Response
    {
        if (!$this->isAuth())
            return $this->redirectToRoute('connexion');
        $description = $request->request->get("description");
        $blog_id = $request->request->get("blog_id");
        $input = [
            'blog_id' => $blog_id,
            'description' => $description,
        ];
        $constraints = new Assert\Collection([
            'blog_id' => [new Assert\NotBlank],
            'description' => [new Assert\NotBlank],
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
            return $this->redirectToRoute('blog_detail', [ 'id'=>$blog_id ]);
        }
        
        $blogcomment = new BlogComment();
        $blog_id = $request->request->get('blog_id');
        $blogcomment->setBlogId($blog_id);
        $description = $request->request->get('description');
        $blogcomment->setDescription($description);
        $blogcomment->setUserId($this->session->get('user')->getId());
        $date = new DateTime();
        $blogcomment->setDate($date);
        $blogcomment->setReplyId(0);
        
        // save
        $doct = $this->getDoctrine()->getManager();
        $doct->persist($blogcomment);
        $doct->flush();
        return $this->redirectToRoute('blog_detail', [ 'id'=>$blog_id ]);
    }

    /**
     * @Route("/blogcomment/reply", name="blog_comment_reply")
     */
    public function reply(Request $request, ValidatorInterface $validator): Response
    {
        if (!$this->isAuth())
            return $this->redirectToRoute('connexion');
        $description = $request->request->get("description");
        $blog_id = $request->request->get("blog_id");
        $reply_id = $request->request->get("reply_id");
        $input = [
            'blog_id' => $blog_id,
            'reply_id' => $reply_id,
            'description' => $description,
        ];
        $constraints = new Assert\Collection([
            'blog_id' => [new Assert\NotBlank],
            'reply_id' => [new Assert\NotBlank],
            'description' => [new Assert\NotBlank],
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
            return $this->redirectToRoute('blog_detail', [ 'id'=>$blog_id ]);
        }
        
        $blogcomment = new BlogComment();
        $blog_id = $request->request->get('blog_id');
        $blogcomment->setBlogId($blog_id);
        $description = $request->request->get('description');
        $blogcomment->setDescription($description);
        $blogcomment->setUserId($this->session->get('user')->getId());
        $date = new DateTime();
        $blogcomment->setDate($date);
        $reply_id = $request->request->get('reply_id');
        $blogcomment->setReplyId($reply_id);
        
        // save
        $doct = $this->getDoctrine()->getManager();
        $doct->persist($blogcomment);
        $doct->flush();
        return $this->redirectToRoute('blog_detail', [ 'id'=>$blog_id ]);
        
    }
}
