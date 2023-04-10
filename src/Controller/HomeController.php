<?php
namespace App\Controller;
   
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Listing;
use App\Entity\Review;
use App\Entity\CategoryType;
use App\Entity\City;
use App\Entity\Blog;
use App\Entity\Blogtype;
use App\Entity\User;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
        
        $categories = $this->getDoctrine()->getRepository(CategoryType::class)->findAll();
        foreach ($categories as $index => $category) {
            $tmp = $this->getDoctrine()->getRepository(Listing::class)->findWithCategoryId($category->getId());
            $category->listing_count = count($tmp);
            $category->color = $this->categoryColor($index);
        }
        $cities = $this->getDoctrine()->getRepository(City::class)->findAll();
        foreach ($cities as $index => $city) {
            $tmp = $this->getDoctrine()->getRepository(Listing::class)->findWithCityId($city->getId());
            $city->listing_count = count($tmp);
        }
        $reviews_init = $this->getDoctrine()->getRepository(Review::class)->findAll();
        $cities = $this->getDoctrine()->getRepository(City::class)->findAll();
        $reviews = [];
        foreach ($reviews_init as $review) {
            if ($review->getFeature())
                array_push($reviews, $review);
        }
        $blogs = $this->getDoctrine()->getRepository(Blog::class)->findAll();
        $blogs = array_slice($blogs, 0, 3);
        foreach ($blogs as $blog) {
            $blog->type = $this->getDoctrine()->getRepository(Blogtype::class)->find($blog->getTypeId());
        }
        return $this->render('pages/home/index.html.twig', [
            'listings' => $listings,
            'reviews' => $reviews,
            'categories' => $categories,
            'cities' => $cities,
            'blogs' => $blogs
        ]);
    }

    private function categoryColor($val) {
        $colors = ['explore-item dark-sky-blue', 'explore-item dodger-blue', 'explore-item yellow', 'explore-item rosy-pink', 'explore-item dark-sky-blue', 'explore-item dodger-blue'];
        return $colors[$val%6];
    }
}