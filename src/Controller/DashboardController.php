<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\City;
use App\Entity\CategoryType;
use App\Entity\ActiveType;
use App\Entity\User;
use App\Entity\Blog;
use App\Entity\BlogComment;
use App\Entity\UserRequest;
use App\Entity\Review;
use App\Entity\Booking;
use App\Entity\Setting;
use App\Entity\Listing;
use App\Entity\Message;
use App\Controller\UserController;
use \DateTime;

class DashboardController extends AbstractController
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
     * @Route("/dashboard", name="dashboard")
     */
    public function index(): Response
    {
        if (!$this->isAuth())
            return $this->redirectToRoute('connexion');
        $listings = $this->getDoctrine()->getRepository(Listing::class)->findWithUserId($this->session->get('user')->getId());
        $reviews = [];
        $bookings = [];
        $visit_number = 0;
        foreach ($listings as $listing) {
            $reviews_tmp = $this->getDoctrine()->getRepository(Review::class)->findAllWithListingId($listing->getId());
            $reviews = array_merge($reviews, $reviews_tmp);
            $bookings_tmp = $this->getDoctrine()->getRepository(Booking::class)->findWithListingId($listing->getId());
            $bookings = array_merge($bookings, $bookings_tmp);
            $visit_number = $visit_number + $listing->getVisitNumber();
        }
        $settings = $this->getDoctrine()->getRepository(Setting::class)->findAll();
        $active_listings = $this->getDoctrine()->getRepository(Listing::class)->findAllActiveWithUser($this->session->get('user')->getId());
        $listing_number = count($active_listings);
        $booking_number = count($bookings);
        $review_number = count($reviews);
        if ($visit_number == 0)
            $booking_per_visit = 'N/A';
        else
            $booking_per_visit = number_format($booking_number/$visit_number, 2);
        if ($booking_number == 0)
            $review_per_booking = 'N/A';
        else
            $review_per_booking = number_format($review_number/$booking_number, 2);
        if ($review_number == 0)
            $percentage = 0;
        else {
            $tmp =0;
            foreach ($reviews as $review) {
                $tmp = $tmp + $review->getRate();
            }
            $percentage = number_format($tmp/$review_number, 2);
        }

        $new_reviews = $this->getDoctrine()->getRepository(Review::class)->findLatest($listings);
        foreach ($new_reviews as $new_review) {
            $listing = $this->getDoctrine()->getRepository(Listing::class)->find($new_review->getListingId());
            $city = $this->getDoctrine()->getRepository(City::class)->find($listing->getCityId());
            $new_review->city = $city->getName();
        }
        $new_bookings = $this->getDoctrine()->getRepository(Booking::class)->findLatest($listings);
        foreach ($new_bookings as $new_booking) {
            $listing = $this->getDoctrine()->getRepository(Listing::class)->find($new_booking->getListingId());
            $city = $this->getDoctrine()->getRepository(City::class)->find($listing->getCityId());
            $new_booking->city = $city->getName();
        }
        $messages = $this->getDoctrine()->getRepository(Message::class)->findLatest($listings);
        foreach ($messages as $message) {
            $now = new DateTime();
            $interval = date_diff($message->getDate(), $now);
            $time_message = "";
            if ($interval->y != 0)
                $time_message = $interval->y . " year ago";
            else if ($interval->m != 0)
                $time_message = $interval->m . " month ago";
            else if ($interval->d != 0)
                $time_message = $interval->d . " day ago";
            else if ($interval->h != 0)
                $time_message = $interval->h . " hour ago";
            else if ($interval->i != 0)
                $time_message = $interval->i . " minute ago";
            else if ($interval->s != 0)
                $time_message = $interval->s . " second ago";
            $message->time_message = $time_message;
        }

        return $this->render('pages/dashboard/index.html.twig', [
            'page' => 'dashboard',
            'subtitle' => 'Welcome back! ' . $this->session->get('user')->getPrenom(),
            'listing_number' => $listing_number,
            'booking_number' => $booking_number,
            'review_number' => $review_number,
            'visit_number' => $visit_number,
            'booking_per_visit' => $booking_per_visit,
            'review_per_booking' => $review_per_booking,
            'percentage' => $percentage,
            'new_bookings' => $new_bookings,
            'new_reviews' => $new_reviews,
            'messages' => $messages,
        ]);
    }

    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function admin_index(): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $cities = $this->getDoctrine()->getRepository(City::class)->findAll();
        $category_types = $this->getDoctrine()->getRepository(CategoryType::class)->findAll();
        $active_types = $this->getDoctrine()->getRepository(ActiveType::class)->findAll();
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $blogs = $this->getDoctrine()->getRepository(Blog::class)->findAll();
        $blog_comments = $this->getDoctrine()->getRepository(BlogComment::class)->findAll();
        $requets = $this->getDoctrine()->getRepository(UserRequest::class)->findAll();
        $reviews = $this->getDoctrine()->getRepository(Review::class)->findAll();
        $bookings = $this->getDoctrine()->getRepository(Booking::class)->findAll();
        $settings = $this->getDoctrine()->getRepository(Setting::class)->findAll();
        $listings = $this->getDoctrine()->getRepository(Listing::class)->findAllActive();
        $listing_number = count($listings);
        $booking_number = count($bookings);
        $review_number = count($reviews);
        if (count($settings) == 0)
            $visit_number = 0;
        else
            $visit_number = $settings[0]->getVisitNumber();
        if ($visit_number == 0)
            $booking_per_visit = 'N/A';
        else
            $booking_per_visit = number_format($booking_number/$visit_number, 2);
        if ($booking_number == 0)
            $review_per_booking = 'N/A';
        else
            $review_per_booking = number_format($review_number/$booking_number, 2);
        if ($review_number == 0)
            $percentage = 0;
        else {
            $tmp =0;
            foreach ($reviews as $review) {
                $tmp = $tmp + $review->getRate();
            }
            $percentage = number_format($tmp/$review_number, 2);
        }
        
        return $this->render('pages/admin/dashboard/index.html.twig', [
            'cities_number' => count($cities),
            'category_number' => count($category_types),
            'placetype_number' => count($active_types),
            'user_number' => count($users),
            'blog_number' => count($blogs),
            'blogcomment_number' => count($blog_comments),
            'request_number' => count($requets),
            'listing_number' => $listing_number,
            'booking_number' => $booking_number,
            'review_number' => $review_number,
            'visit_number' => $visit_number,
            'booking_per_visit' => $booking_per_visit,
            'review_per_booking' => $review_per_booking,
            'percentage' => $percentage
        ]);
    }
}