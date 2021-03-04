<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WishlistController extends AbstractController
{
    /**
     * @Route("/wishlist", name="wishlist")
     */
    public function index(): Response
    {
        return $this->render('pages/wishlist/index.html.twig', [
        ]);
    }
}