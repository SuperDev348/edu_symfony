<?php
namespace App\Controller;
   
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SingleController extends AbstractController
{
    /**
     * @Route("/single", name="single")
     */
    public function index(): Response
    {
        return $this->render('pages/single/index.html.twig', [
        ]);
    }
}