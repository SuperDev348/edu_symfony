<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class WishlistController extends AbstractController
{
    protected $session;
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
    /**
     * @Route("/wishlist", name="wishlist")
     */
    public function index(): Response
    {
        if(is_null($this->session->get('user'))){
            return $this->redirectToRoute('connexion');
        }
        return $this->render('pages/wishlist/index.html.twig', [
            'page' => 'wishlist',
            'subtitle' => 'Wishlish',
        ]);
    }
}