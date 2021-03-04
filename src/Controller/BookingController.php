<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Booking;
use App\Entity\Listing;

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
}