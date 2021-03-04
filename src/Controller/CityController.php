<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CityController extends AbstractController
{
    /**
     * @Route("/citydetail2", name="citydetail2")
     */
    public function detail2(): Response
    {
        return $this->render('pages/city/detail2.html.twig', [
        ]);
    }

    /**
     * @Route("/citydetail3", name="citydetail3")
     */
    public function detail3(): Response
    {
        return $this->render('pages/city/detail3.html.twig', [
        ]);
    }
}