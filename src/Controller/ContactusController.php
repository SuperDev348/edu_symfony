<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactusController extends AbstractController
{
    /**
     * @Route("/contactus", name="contactus")
     */
    public function index(): Response
    {
        return $this->render('pages/contactus/index.html.twig', [
        ]);
    }
}