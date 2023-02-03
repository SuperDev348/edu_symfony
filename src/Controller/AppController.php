<?php
namespace App\Controller;
   
use Symfony\Component\HttpFoundation\Response;

class AppController extends AbstractController
{
    /**
     * @Route("/app", name="app")
     */
    public function index(): Response
    {
        return $this->render('pages/app/index.html.twig', [
        ]);
    }
}