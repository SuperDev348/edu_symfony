<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TemplateController extends AbstractController
{
    /**
     * @Route("/404", name="error_404")
     */
    public function error_404(): Response
    {
        return $this->render('pages/error/error.html.twig', [
        ]);
    }

    /**
     * @Route("/comingsoon", name="comingsoon")
     */
    public function comingsoon(): Response
    {
        return $this->render('pages/comingsoon/index.html.twig', [
        ]);
    }
}