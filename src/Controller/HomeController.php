<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Listing;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
        return $this->render('pages/home/index.html.twig', [
            'listings' => $listings
        ]);
    }
}