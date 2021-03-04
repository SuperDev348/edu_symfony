<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(): Response
    {
        return $this->render('pages/blog/index.html.twig', [
        ]);
    }

    /**
     * @Route("/blog/detail", name="blog_detail")
     */
    public function detail(): Response
    {
        return $this->render('pages/blog/detail.html.twig', [
        ]);
    }
}