<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use App\Entity\Booking;
use App\Entity\Listing;
use App\Entity\Review;

class BookingController extends AbstractController
{
    /**
     * @Route("/booking", name="booking")
     */
    public function index(): Response
    {
        $bookings = $this->getDoctrine()->getRepository(Booking::class)->findAll();
        foreach ($bookings as $booking) {
            $listing = $this->getDoctrine()->getRepository(Listing::class)->find($booking->getListingId());
            $booking->list_name = $listing->getName();
        }
        return $this->render('pages/booking/index.html.twig', [
            'page' => 'booking',
            'subtitle' => 'Bookings',
            'bookings' => $bookings
        ]);
    }

    /**
     * @Route("/booking/create", name="booking_create")
     */
    public function create(): Response
    {
        return $this->render('pages/booking/create.html.twig', [
            
        ]);
    }

    /**
     * @Route("/booking/store", name="booking_store")
     */
    public function store(Request $request, ValidatorInterface $validator): Response
    {
        $adult_number = $request->request->get("adult_number");
        $children_number = $request->request->get("children_number");
        $date = $request->request->get("date");
        $time = $request->request->get("time");
        $listing_id = $request->request->get('listing_id');
        $input = [
            'adult_number' => $adult_number,
            'children_number' => $children_number,
            'date' => $date,
            'time' => $time
        ];
        $constraints = new Assert\Collection([
            'adult_number' => [new Assert\NotBlank],
            'children_number' => [new Assert\NotBlank],
            'time' => [new Assert\NotBlank],
            'date' => [new Assert\NotBlank],
        ]);
        $violations = $validator->validate($input, $constraints);
        if (count($violations) > 0) {
            $accessor = PropertyAccess::createPropertyAccessor();
            $errorMessages = [];
            foreach ($violations as $violation) {
                $accessor->setValue($errorMessages,
                $violation->getPropertyPath(),
                $violation->getMessage());
            }
            $listing = $this->getDoctrine()->getRepository(Listing::class)->find($listing_id);
            $reviews = $this->getDoctrine()->getRepository(Review::class)->findAllWithListingId($listing->getId());
            return $this->render('pages/listing/detail.html.twig', [
                'listing' => $listing,
                'reviews' => $reviews,
                'errors' => $errorMessages,
                'old' => $input
            ]);
        }

        $booking = new Booking();
        $booking->setStatus('pending');
        $adult_number = $request->request->get('adult_number');
        $booking->setAdultNumber($adult_number);
        $children_number = $request->request->get('children_number');
        $booking->setChildrenNumber($children_number);
        $listing_id = $request->request->get('listing_id');
        $booking->setListingId($listing_id);
        $time = $request->request->get('time');
        $booking->setTime($time);
        $date = $request->request->get('date');
        $booking->setDate(\DateTime::createFromFormat("d.m.Y", $date));
        
        // validate
        $errors = $validator->validate($booking);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }
        // save
        $doct = $this->getDoctrine()->getManager();
        $doct->persist($booking);
        $doct->flush();
        return $this->redirectToRoute('booking');
    }

    /**
     * @Route("/booking/edit/{id}", name="booking_edit")
     */
    public function edit($id): Response
    {
        $booking = $this->getDoctrine()->getRepository(Booking::class)->find($id);
        return $this->render('pages/booking/edit.html.twig', [
            'booking' => $booking
        ]);
    }

    /**
     * @Route("/booking/update/{id}", name="booking_update")
     */
    public function update($id, Request $request, ValidatorInterface $validator): Response
    {
        $doct = $this->getDoctrine()->getManager();
        $booking = $doct->getRepository(Booking::class)->find($id);
        $adult_number = $request->request->get('adult_number');
        $booking->setAdultNumber($adult_number);
        $children_number = $request->request->get('children_number');
        $booking->setChildrenNumber($children_number);
        $listing_id = $request->request->get('listing_id');
        $booking->setListingId($listing_id);
        $time = $request->request->get('time');
        $booking->setTime($time);
        
        // validate
        $errors = $validator->validate($booking);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }
        // update
        $doct->flush();
        return $this->redirectToRoute('booking', [
            'id' => $booking->getId()
        ]);
    }

    /**
     * @Route("/booking/delete/{id}", name="booking_delete")
     */
    public function delete($id): Response
    {
        $doct = $this->getDoctrine()->getManager();
        $booking = $doct->getRepository(Booking::class)->find($id);
        $doct->remove($booking);
        $doct->flush();
        return $this->redirectToRoute('booking', [
            'id' => $booking->getId()
        ]);
    }

    /**
     * @Route("/booking/status/{id}/{status}", name="booking_status")
     */
    public function setStatus($id, $status, ValidatorInterface $validator): Response
    {
        $doct = $this->getDoctrine()->getManager();
        $booking = $doct->getRepository(Booking::class)->find($id);
        $booking->setStatus($status);
        
        // validate
        $errors = $validator->validate($booking);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }
        // update
        $doct->flush();
        return $this->redirectToRoute('booking', [
            'id' => $booking->getId()
        ]);
    }

    private function getStatusList() {
        $res = [
            "approved",
            "pending",
            "cancel"
        ];
        return $res;
    }

    private function getTimeList() {
        $res = [
            "12:00 AM",
            "12:30 AM",
            "1:00 AM",
            "1:30 AM",
            "2:00 AM",
            "2:30 AM",
            "3:00 AM",
            "3:30 AM",
            "4:00 AM",
            "4:30 AM",
            "5:00 AM",
            "5:30 AM",
            "6:00 AM",
            "6:30 AM",
            "7:00 AM",
            "7:30 AM",
            "8:00 AM",
            "8:30 AM",
            "9:00 AM",
            "9:30 AM",
            "10:00 AM",
            "10:30 AM",
            "11:00 AM",
            "11:30 AM",
            "12:00 PM",
            "12:30 PM",
            "1:00 PM",
            "1:30 PM",
            "2:00 PM",
            "2:30 PM",
            "3:00 PM",
            "3:30 PM",
            "4:00 PM",
            "4:30 PM",
            "5:00 PM",
            "5:30 PM",
            "6:00 PM",
            "6:30 PM",
            "7:00 PM",
            "7:30 PM",
            "8:00 PM",
            "8:30 PM",
            "9:00 PM",
            "9:30 PM",
            "10:00 PM",
            "10:30 PM",
            "11:00 PM",
            "11:30 PM"
        ];
        return $res;
    }

    /**
     * @Route("/admin/booking", name="admin_booking")
     */
    public function admin_index(): Response
    {
        $bookings = $this->getDoctrine()->getRepository(Booking::class)->findAll();
        $listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
        foreach ($bookings as $booking) {
            $listing = $this->getDoctrine()->getRepository(Listing::class)->find($booking->getListingId());
            $booking->list_name = $listing->getName();
        }
        return $this->render('pages/admin/booking/index.html.twig', [
            'bookings' => $bookings,
            'listings' => $listings
        ]);
    }

    /**
     * @Route("/admin/booking/create", name="admin_booking_create")
     */
    public function admin_create(): Response
    {
        $listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
        $statuslist = $this->getStatusList();
        $timelist = $this->getTimeList();
        return $this->render('pages/admin/booking/create.html.twig', [
            'listings' => $listings,
            'statuslist' => $statuslist,
            'timelist' => $timelist
        ]);
    }

    /**
     * @Route("/admin/booking/store", name="admin_booking_store")
     */
    public function admin_store(Request $request, ValidatorInterface $validator): Response
    {
        $adult_number = $request->request->get("adult_number");
        $children_number = $request->request->get("children_number");
        $time = $request->request->get("time");
        $date = $request->request->get("date");
        $input = [
            'adult_number' => $adult_number,
            'children_number' => $children_number,
            'time' => $time,
            'date' => $date
        ];
        $constraints = new Assert\Collection([
            'adult_number' => [new Assert\NotBlank],
            'children_number' => [new Assert\NotBlank],
            'time' => [new Assert\NotBlank],
            'date' => [new Assert\NotBlank],
        ]);
        $violations = $validator->validate($input, $constraints);
        if (count($violations) > 0) {
            $accessor = PropertyAccess::createPropertyAccessor();
            $errorMessages = [];
            foreach ($violations as $violation) {
                $accessor->setValue($errorMessages,
                $violation->getPropertyPath(),
                $violation->getMessage());
            }
            $listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
            $statuslist = $this->getStatusList();
            $timelist = $this->getTimeList();
            return $this->render('pages/admin/booking/create.html.twig', [
                'listings' => $listings,
                'statuslist' => $statuslist,
                'timelist' => $timelist,
                'errors' => $errorMessages,
                'old' => $input
            ]);
        }
        
        $booking = new Booking();
        $booking->setStatus('pending');
        $adult_number = $request->request->get('adult_number');
        $booking->setAdultNumber($adult_number);
        $children_number = $request->request->get('children_number');
        $booking->setChildrenNumber($children_number);
        $listing_id = $request->request->get('listing_id');
        $booking->setListingId($listing_id);
        $time = $request->request->get('time');
        $booking->setTime($time);
        $date = $request->request->get('date');
        $booking->setDate(\DateTime::createFromFormat("d/m/Y", $date));
        
        // save
        $doct = $this->getDoctrine()->getManager();
        $doct->persist($booking);
        $doct->flush();
        return $this->redirectToRoute('admin_booking');
    }

    /**
     * @Route("/admin/booking/edit/{id}", name="admin_booking_edit")
     */
    public function admin_edit($id): Response
    {
        $booking = $this->getDoctrine()->getRepository(Booking::class)->find($id);
        $listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
        $statuslist = $this->getStatusList();
        $timelist = $this->getTimeList();
        return $this->render('pages/admin/booking/edit.html.twig', [
            'booking' => $booking,
            'listings' => $listings,
            'statuslist' => $statuslist,
            'timelist' => $timelist
        ]);
    }

    /**
     * @Route("/admin/booking/update/{id}", name="admin_booking_update")
     */
    public function admin_update($id, Request $request, ValidatorInterface $validator): Response
    {
        $adult_number = $request->request->get("adult_number");
        $children_number = $request->request->get("children_number");
        $time = $request->request->get("time");
        $date = $request->request->get("date");
        $input = [
            'adult_number' => $adult_number,
            'children_number' => $children_number,
            'time' => $time,
            'date' => $date
        ];
        $constraints = new Assert\Collection([
            'adult_number' => [new Assert\NotBlank],
            'children_number' => [new Assert\NotBlank],
            'time' => [new Assert\NotBlank],
            'date' => [new Assert\NotBlank],
        ]);
        $violations = $validator->validate($input, $constraints);
        if (count($violations) > 0) {
            $accessor = PropertyAccess::createPropertyAccessor();
            $errorMessages = [];
            foreach ($violations as $violation) {
                $accessor->setValue($errorMessages,
                $violation->getPropertyPath(),
                $violation->getMessage());
            }
            $booking = $this->getDoctrine()->getRepository(Booking::class)->find($id);
            $listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
            $statuslist = $this->getStatusList();
            $timelist = $this->getTimeList();
            return $this->render('pages/admin/booking/edit.html.twig', [
                'booking' => $booking,
                'listings' => $listings,
                'statuslist' => $statuslist,
                'timelist' => $timelist,
                'errors' => $errorMessages,
                'old' => $input
            ]);
        }

        $doct = $this->getDoctrine()->getManager();
        $booking = $doct->getRepository(Booking::class)->find($id);
        $adult_number = $request->request->get('adult_number');
        $booking->setAdultNumber($adult_number);
        $children_number = $request->request->get('children_number');
        $booking->setChildrenNumber($children_number);
        $listing_id = $request->request->get('listing_id');
        $booking->setListingId($listing_id);
        $time = $request->request->get('time');
        $booking->setTime($time);
        $status = $request->request->get('status');
        $booking->setStatus($status);
        // validate
        $errors = $validator->validate($booking);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }
        // update
        $doct->flush();
        return $this->redirectToRoute('admin_booking', [
            'id' => $booking->getId()
        ]);
    }

    /**
     * @Route("/admin/booking/delete/{id}", name="admin_booking_delete")
     */
    public function admin_delete($id): Response
    {
        $doct = $this->getDoctrine()->getManager();
        $booking = $doct->getRepository(Booking::class)->find($id);
        $doct->remove($booking);
        $doct->flush();
        return $this->redirectToRoute('admin_booking', [
            'id' => $booking->getId()
        ]);
    }
}