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
use App\Entity\User;
use App\Entity\BlogCommentLike;
use App\Entity\BlogComment;

class BlogCommentLikeController extends AbstractController
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
     * @Route("/blogcomment/like/detach/{id}", name="blog_comment_like_detach")
     */
    public function detach($id): Response
    {
        $likes = $this->getDoctrine()->getRepository(BlogCommentLike::class)->findAll();
        foreach ($likes as $like) {
            if ($id == $like->getBlogcommentId()) {
                $doct = $this->getDoctrine()->getManager();
                $like = $doct->getRepository(BlogCommentLike::class)->find($like->getId());
                $doct->remove($like);
                $doct->flush();
            }
        }
        $blogcomment = $this->getDoctrine()->getRepository(BlogComment::class)->find($id);
        return $this->redirectToRoute('blog_detail', [
            'id' => $blogcomment->getBlogId()
        ]);
    }
}
