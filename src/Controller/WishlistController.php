<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Wishlist;
use App\Entity\Listing;
use App\Entity\CategoryType;
use App\Entity\City;
use App\Entity\Review;
use App\Entity\User;

class WishlistController extends AbstractController
{
    protected $session;
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    private function isAuth() {
        if(is_null($this->session->get('user'))){
            return false;
        }
        $user = $this->getDoctrine()->getRepository(User::class)->find($this->session->get('user')->getId());
        if ($user->getBan()) {
            $this->session->clear();
            return false;
        }
        return true;
    }

    private function isAdmin() {
        if(is_null($this->session->get('user'))||$this->session->get('user')->getType()!="admin"){
            return false;
        }
        $user = $this->getDoctrine()->getRepository(User::class)->find($this->session->get('user')->getId());
        if ($user->getBan()) {
            $this->session->clear();
            return false;
        }
        return true;
    }

    /**
     * @Route("/wishlist", name="wishlist")
     */
    public function index(): Response
    {
        if (!$this->isAuth())
            return $this->redirectToRoute('connexion');
        $cities = $this->getDoctrine()->getRepository(City::class)->findAll();
        $categories = $this->getDoctrine()->getRepository(CategoryType::class)->findAll();
        $wishlists = $this->getDoctrine()->getRepository(Wishlist::class)->findWithUserId($this->session->get('user')->getId());
        $listings = [];
        foreach ($wishlists as $wishlist) {
            $listing = $this->getDoctrine()->getRepository(Listing::class)->find($wishlist->getListingId());
            $listing->category = $this->getDoctrine()->getRepository(CategoryType::class)->find($listing->getCategoryId());
            $listing->city = $this->getDoctrine()->getRepository(City::class)->find($listing->getCityId());
            $listing->user = $this->getDoctrine()->getRepository(User::class)->find($listing->getUserId());
            $review = $this->review($listing);
            $listing->review_rate = $review['review_rate'];
            $listing->review_count = count($review['reviews']);
            array_push($listings, $listing);
        }
        return $this->render('pages/wishlist/index.html.twig', [
            'page' => 'wishlist',
            'subtitle' => 'Wishlish',
            'listings' => $listings,
            'cities' => $cities,
            'categories' => $categories
        ]);
    }

    private function review($listing) {
        $reviews = $this->getDoctrine()->getRepository(Review::class)->findAllWithListingId($listing->getId());
        $review_rate = 0;
        if (count($reviews) != 0) {
            foreach($reviews as $review) {
                $review_rate = $review_rate + $review->getRate();
            }
            $review_rate = number_format($review_rate/count($reviews), 2);
        }
        $res = ['reviews' => $reviews, 'review_rate' => $review_rate];
        return $res;
    }

    /**
     * @Route("/wishlist/attach/{id}", name="wishlist_attach")
     */
    public function attach($id): Response
    {
        if (!$this->isAuth())
            return $this->redirectToRoute('connexion');
        $user_id = $this->session->get('user')->getId();
        $wishlists = $this->getDoctrine()->getRepository(Wishlist::class)->findWithUserId($user_id);
        $ids = [];
        foreach ($wishlists as $wishlist) {
            array_push($ids, $wishlist->getListingId());
        }
        if (!in_array($id, $ids)) {
            $wishlist = new Wishlist();
            $wishlist->setListingId($id);
            $wishlist->setUserId($user_id);
            $doct = $this->getDoctrine()->getManager();
            $doct->persist($wishlist);
            $doct->flush();
        }
        return $this->redirectToRoute('listing_detail', [
            'id' => $id
        ]);
    }

    /**
     * @Route("/wishlist/detach/{id}", name="wishlist_detach")
     */
    public function detach($id): Response
    {
        if (!$this->isAuth())
            return $this->redirectToRoute('connexion');
        $user_id = $this->session->get('user')->getId();
        $wishlists = $this->getDoctrine()->getRepository(Wishlist::class)->findWithUserId($user_id);
        foreach ($wishlists as $wishlist) {
            if ($id == $wishlist->getListingId()) {
                $doct = $this->getDoctrine()->getManager();
                $wishlist = $doct->getRepository(Wishlist::class)->find($wishlist->getId());
                $doct->remove($wishlist);
                $doct->flush();
            }
        }
        return $this->redirectToRoute('wishlist', [
        ]);
    }

    /**
     * @Route("/wishlist/search", name="wishlist_search")
     */
    public function search(Request $request): Response
    {
        if (!$this->isAuth())
            return $this->redirectToRoute('connexion');
        $category_id = $request->request->get('category_id');
        $city_id = $request->request->get('city_id');
        $id = $request->request->get('id');
        $name = $request->request->get('name');
        $filter = [];
        if ($category_id != '0')
            $filter['category_id'] = $category_id;
        if ($city_id != '0')
            $filter['city_id'] = $city_id;
        if ($id != '')
            $filter['id'] = $id;
        if ($name != '')
            $filter['name'] = $name;
        
        $doct = $this->getDoctrine()->getManager();
        $listings_tmp = $doct->getRepository(Listing::class)->findWithFilter($filter);
        $listings = [];
        $wishlists = $doct->getRepository(Wishlist::class)->findWithUserId($this->session->get('user')->getId());
        $ids = [];
        foreach ($wishlists as $wishlist) {
            array_push($ids, $wishlist->getListingId());
        }
        foreach ($listings_tmp as $listing) {
            if (in_array($listing->getId(), $ids)) {
                $listing->category = $this->getDoctrine()->getRepository(CategoryType::class)->find($listing->getCategoryId());
                $listing->city = $this->getDoctrine()->getRepository(City::class)->find($listing->getCityId());
                $review = $this->review($listing);
                $listing->review_rate = $review['review_rate'];
                $listing->review_count = count($review['reviews']);
                array_push($listings, $listing);
            }
        }
        $cities = $this->getDoctrine()->getRepository(City::class)->findAll();
        $categories = $this->getDoctrine()->getRepository(CategoryType::class)->findAll();
        return $this->render('pages/wishlist/index.html.twig', [
            'page' => 'wishlist',
            'subtitle' => 'Wishlish',
            'listings' => $listings,
            'cities' => $cities,
            'categories' => $categories,
            'filter' => $filter,
        ]);
    }

    private function getCityList() {
        $res = [
            "Paris",
            "New York",
            "Chicago"
        ];
        return $res;
    }

    private function getCategoryList() {
        $res = [
            "Restaurant",
            "Gym",
            "Beaty & Spa",
            "Shopping"
        ];
        return $res;
    }

    /**
     * @Route("/admin/wishlist", name="admin_wishlist")
     */
    public function admin_index(): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $cities = $this->getCityList();
        $categories = $this->getCategoryList();
        $wishlists = $this->getDoctrine()->getRepository(Wishlist::class)->findAll();
        $listings = [];
        foreach ($wishlists as $wishlist) {
            $listing = $this->getDoctrine()->getRepository(Listing::class)->find($wishlist->getListingId());
            array_push($listings, $listing);
        }
        return $this->render('pages/admin/wishlist/index.html.twig', [
            'page' => 'wishlist',
            'subtitle' => 'Wishlish',
            'listings' => $listings,
            'cities' => $cities,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/admin/wishlist/attach/{id}", name="admin_wishlist_attach")
     */
    public function admin_attach($id): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $wishlists = $this->getDoctrine()->getRepository(Wishlist::class)->findAll();
        $ids = [];
        foreach ($wishlists as $wishlist) {
            array_push($ids, $wishlist->getListingId());
        }
        if (!in_array($id, $ids)) {
            $wishlist = new Wishlist();
            $wishlist->setListingId($id);
            $doct = $this->getDoctrine()->getManager();
            $doct->persist($wishlist);
            $doct->flush();
        }
        return $this->redirectToRoute('admin_wishlist', [
            'id' => $id
        ]);
    }

    /**
     * @Route("/admin/wishlist/detach/{id}", name="admin_wishlist_detach")
     */
    public function admin_detach($id): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $wishlists = $this->getDoctrine()->getRepository(Wishlist::class)->findAll();
        foreach ($wishlists as $wishlist) {
            if ($id == $wishlist->getListingId()) {
                $doct = $this->getDoctrine()->getManager();
                $wishlist = $doct->getRepository(Wishlist::class)->find($wishlist->getId());
                $doct->remove($wishlist);
                $doct->flush();
            }
        }
        return $this->redirectToRoute('admin_wishlist', [
        ]);
    }

    /**
     * @Route("/admin/wishlist/search", name="admin_wishlist_search")
     */
    public function admin_search(Request $request): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $category = $request->request->get('category');
        $city = $request->request->get('city');
        $id = $request->request->get('id');
        $name = $request->request->get('name');
        $filter = [];
        if ($category != '0')
            $filter['category'] = $category;
        if ($city != '0')
            $filter['city'] = $city;
        if ($id != '')
            $filter['id'] = $id;
        if ($name != '')
            $filter['name'] = $name;
        
        $doct = $this->getDoctrine()->getManager();
        $listings_tmp = $doct->getRepository(Listing::class)->findWithFilter($filter);
        $listings = [];
        $wishlists = $doct->getRepository(Wishlist::class)->findAll();
        $ids = [];
        foreach ($wishlists as $wishlist) {
            array_push($ids, $wishlist->getListingId());
        }
        foreach ($listings_tmp as $listing) {
            if (in_array($listing->getId(), $ids)) {
                array_push($listings, $listing);
            }
        }
        $cities = $this->getCityList();
        $categories = $this->getCategoryList();
        return $this->render('pages/admin/wishlist/index.html.twig', [
            'page' => 'wishlist',
            'subtitle' => 'Wishlish',
            'listings' => $listings,
            'cities' => $cities,
            'categories' => $categories,
            'filter' => $filter,
        ]);
    }
}