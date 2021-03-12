<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Review;
use App\Entity\Booking;
use App\Entity\Listing;
use App\Entity\Setting;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(): Response
    {
        $reviews = $this->getDoctrine()->getRepository(Review::class)->findAll();
        $bookings = $this->getDoctrine()->getRepository(Booking::class)->findAll();
        $settings = $this->getDoctrine()->getRepository(Setting::class)->findAll();
        $listings = $this->getDoctrine()->getRepository(Listing::class)->findAllActive();
        $listing_number = count($listings);
        $booking_number = count($bookings);
        $review_number = count($reviews);
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

        $new_reviews = $this->getDoctrine()->getRepository(Review::class)->findLatest();
        foreach ($new_reviews as $new_review) {
            $listing = $this->getDoctrine()->getRepository(Listing::class)->find($new_review->getListingId());
            $new_review->city = $listing->getCity();
        }
        $new_bookings = $this->getDoctrine()->getRepository(Booking::class)->findLatest();
        foreach ($new_bookings as $new_booking) {
            $listing = $this->getDoctrine()->getRepository(Listing::class)->find($new_booking->getListingId());
            $new_booking->city = $listing->getCity();
        }

        return $this->render('pages/dashboard/index.html.twig', [
            'page' => 'dashboard',
            'subtitle' => 'Welcome back! Kevin',
            'listing_number' => $listing_number,
            'booking_number' => $booking_number,
            'review_number' => $review_number,
            'visit_number' => $visit_number,
            'booking_per_visit' => $booking_per_visit,
            'review_per_booking' => $review_per_booking,
            'percentage' => $percentage,
            'new_bookings' => $new_bookings,
            'new_reviews' => $new_reviews
        ]);
    }

    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function admin_index(): Response
    {
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