<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class AboutusController extends AbstractController
{
    /**
     * @Route("/aboutus", name="aboutus")
     */
    public function index(): Response
    {
        return $this->render('pages/aboutus/index.html.twig', [
        ]);
    }
}