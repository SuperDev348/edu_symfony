<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ListingController extends AbstractController
{
    /**
     * @Route("/listing", name="listing")
     */
    public function index(): Response
    {
        return $this->render('pages/listing/index.html.twig', [
            'page' => 'listing',
            'subtitle' => 'My Listings',
        ]);
    }
}