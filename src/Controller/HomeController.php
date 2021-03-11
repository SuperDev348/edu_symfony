<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Listing;
use App\Entity\Review;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
        $reviews_init = $this->getDoctrine()->getRepository(Review::class)->findAll();
        $reviews = [];
        foreach ($reviews_init as $review) {
            if ($review->getFeature())
                array_push($reviews, $review);
        }
        return $this->render('pages/home/index.html.twig', [
            'listings' => $listings,
            'reviews' => $reviews
        ]);
    }
}