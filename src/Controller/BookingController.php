<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use App\Entity\Booking;
use App\Entity\Listing;
use App\Entity\User;
use Twilio\Rest\Client;

class BookingController extends AbstractController
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
     * @Route("/booking", name="booking")
     */
    public function index(): Response
    {
        if (!$this->isAuth())
            return $this->redirectToRoute('connexion');
        if ($this->session->get('user')->getType() == 'client')
            return $this->redirectToRoute('dashboard');
        $listings = $this->getDoctrine()->getRepository(Listing::class)->findWithUserId($this->session->get('user')->getId());
        $bookings = [];
        foreach ($listings as $listing) {
            $tmp = $this->getDoctrine()->getRepository(Booking::class)->findWithListingId($listing->getId());
            foreach ($tmp as $b) {
                $b->list_name = $listing->getName();
            }
            $bookings = array_merge($bookings, $tmp);
        }
        // var_dump($bookings);
        $statusList = $this->getStatusList();
        return $this->render('pages/booking/index.html.twig', [
            'page' => 'booking',
            'subtitle' => 'Bookings',
            'bookings' => $bookings,
            'listings' => $listings,
            'statues' => $statusList
        ]);
    }

    /**
     * @Route("/booking/create", name="booking_create")
     */
    public function create(): Response
    {
        if (!$this->isAuth())
            return $this->redirectToRoute('connexion');
        return $this->render('pages/booking/create.html.twig', [
        ]);
    }

    /**
     * @Route("/booking/store", name="booking_store")
     */
    public function store(Request $request, ValidatorInterface $validator): Response
    {
        if (!$this->isAuth())
            return $this->redirectToRoute('connexion');
        $adult_number = $request->request->get("adult_number");
        $children_number = $request->request->get("children_number");
        $date = $request->request->get("date");
        $time = $request->request->get("time");
        $listing_id = $request->request->get('listing_id');
        $listing = $this->getDoctrine()->getRepository(Listing::class)->find($listing_id);
        $phone_number = $request->request->get('phone_number');
        $input = [
            'adult_number' => $adult_number,
            'children_number' => $children_number,
            'date' => $date,
            'time' => $time,
            'phone_number' => $phone_number
        ];
        $constraints = new Assert\Collection([
            'adult_number' => [new Assert\NotBlank],
            'children_number' => [new Assert\NotBlank],
            'time' => [new Assert\NotBlank],
            'date' => [new Assert\NotBlank],
            'phone_number' => [new Assert\NotBlank],
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
            $reviews = $this->getDoctrine()->getRepository(Review::class)->findAllWithListingId($listing->getId());
            $review_rate = 0;
            if (count($reviews) != 0) {
                foreach($reviews as $review) {
                    $review_rate = $review_rate + $review->getRate();
                }
                $review_rate = number_format($review_rate/count($reviews), 2);
            }
            $suggestions = $this->getDoctrine()->getRepository(Suggestion::class)->findAll();
            return $this->render('pages/listing/detail.html.twig', [
                'listing' => $listing,
                'reviews' => $reviews,
                'errors' => $errorMessages,
                'old' => $input,
                'review_rate' => $review_rate,
                'suggestions' => $suggestions
            ]);
        }

        $doct = $this->getDoctrine()->getManager();
        $settings = $doct->getRepository(Setting::class)->findAll();
        $setting = $settings[0];
        if ($setting->getBookingBlock()) {
            $from = $setting->getBookingBlockFrom();
            $to = $setting->getBookingBlockTo();
            $real_date = \DateTime::createFromFormat("d.m.Y", $date);
            if ($real_date >= $from && $real_date <= $to) {
                $reviews = $this->getDoctrine()->getRepository(Review::class)->findAllWithListingId($listing->getId());
                $review_rate = 0;
                if (count($reviews) != 0) {
                    foreach($reviews as $review) {
                        $review_rate = $review_rate + $review->getRate();
                    }
                    $review_rate = number_format($review_rate/count($reviews), 2);
                }
                $suggestions = $this->getDoctrine()->getRepository(Suggestion::class)->findAll();
                $errorMessages = ['date' => 'this date is blocked'];
                return $this->render('pages/listing/detail.html.twig', [
                    'listing' => $listing,
                    'reviews' => $reviews,
                    'errors' => $errorMessages,
                    'old' => $input,
                    'review_rate' => $review_rate,
                    'suggestions' => $suggestions
                ]);
            }
        }

        $booking = new Booking();
        $booking->setStatus('pending');
        $booking->setAdultNumber($adult_number);
        $booking->setChildrenNumber($children_number);
        $booking->setListingId($listing_id);
        $booking->setTime($time);
        $booking->setDate(\DateTime::createFromFormat("d.m.Y", $date));
        $booking->setPhoneNumber($phone_number);
        // save
        $doct = $this->getDoctrine()->getManager();
        $doct->persist($booking);
        $doct->flush();
        // send message
        $sender = $this->getParameter('twilio_number');
        $sid = $this->getParameter('twilio_sid');
        $token = $this->getParameter('twilio_token');
        $client = new Client($sid, $token);
        try {
            $message = $client->messages->create(
                $listing->getPhone(), // Text this number
                [
                    'from' => $sender, // From a valid Twilio number
                    'body' => 'You have received a new reservation for your listing: ' . $listing->getName()
                ]
            );
        } catch (Exception $e) {
            return $this->render('pages/booking/thankyou.html.twig', [
            ]);
        }  finally {
            return $this->render('pages/booking/thankyou.html.twig', [
            ]);
        }
        
        // dd($message->sid);
        return $this->render('pages/booking/thankyou.html.twig', [
        ]);
    }

    /**
     * @Route("/booking/verify", name="booking_verify")
     */
    public function verify(Request $request, ValidatorInterface $validator): Response
    {
        if (!$this->isAuth())
            return $this->redirectToRoute('connexion');
        $verify_code = $request->request->get("verify_code");
        $input = [
            'verify_code' => $verify_code
        ];
        $constraints = new Assert\Collection([
            'verify_code' => [new Assert\NotBlank]
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
            return $this->render('pages/booking/verify.html.twig', [
                'errors' => $errorMessages,
                'old' => $input
            ]);
        }
        $booking_tmp = $this->get('session')->get('booking');

        if ($booking_tmp['verify_code'] != $verify_code) {
            $errorMessages = ["verify_code" => "please insert a correct code."];
            return $this->render('pages/booking/verify.html.twig', [
                'errors' => $errorMessages
            ]);
        }

        $booking = new Booking();
        $booking->setStatus('pending');
        $booking->setAdultNumber($booking_tmp['adult_number']);
        $booking->setChildrenNumber($booking_tmp['children_number']);
        $booking->setListingId($booking_tmp['listing_id']);
        $booking->setTime($booking_tmp['time']);
        $booking->setDate(\DateTime::createFromFormat("d.m.Y", $booking_tmp['date']));
        $booking->setPhoneNumber($booking_tmp['phone_number']);

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
        if (!$this->isAuth())
            return $this->redirectToRoute('connexion');
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
        if (!$this->isAuth())
            return $this->redirectToRoute('connexion');
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
        if (!$this->isAuth())
            return $this->redirectToRoute('connexion');
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
        if (!$this->isAuth())
            return $this->redirectToRoute('connexion');
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
        $listing = $doct->getRepository(Listing::class)->find($booking->getListingId());
        if ($status == 'approved') {
            // send message
            $sender = $this->getParameter('twilio_number');
            $sid = $this->getParameter('twilio_sid');
            $token = $this->getParameter('twilio_token');
            $client = new Client($sid, $token);
            try {
                $message = $client->messages->create(
                    $booking->getPhoneNumber(), // Text this number
                    [
                        'from' => $sender, // From a valid Twilio number
                        'body' => 'Your booking for "' . $listing->getName() . '" is now confirmed.'
                    ]
                );
            } catch (Exception $e) {
                return $this->redirectToRoute('booking');
            }  finally {
                return $this->redirectToRoute('booking');
            }
        }
        return $this->redirectToRoute('booking', [
            'id' => $booking->getId()
        ]);
    }

    /**
     * @Route("/booking/blockset", name="booking_blockset")
     */
    public function block(Request $request, ValidatorInterface $validator): Response
    {
        if (!$this->isAuth())
            return $this->redirectToRoute('connexion');
        $booking_block = $request->request->get('booking_block');
        $from = "";
        $to = "";
        if ($booking_block == true) {
            $date_range = $request->request->get('date_range'); //03/11/2021 - 03/11/2021
            list($from, $to) = explode(' - ', $date_range);
        }

        $doct = $this->getDoctrine()->getManager();
        $settings = $doct->getRepository(Setting::class)->findAll();
        if (count($settings) == 0) {
            $visit_number = 0;
            $setting = new Setting();
            $setting->setVisitNumber($visit_number);
            $setting->setBookingBlock($booking_block);
            if ($booking_block == true) {
                $setting->setBookingBlockFrom(\DateTime::createFromFormat("m/d/Y", $from));
                $setting->setBookingBlockTo(\DateTime::createFromFormat("m/d/Y", $to));
            }
            $doct->persist($setting);
            $doct->flush();
        }
        else {
            $setting = $settings[0];
            $setting->setBookingBlock($booking_block);
            if ($booking_block == true) {
                $setting->setBookingBlockFrom(\DateTime::createFromFormat("m/d/Y", $from));
                $setting->setBookingBlockTo(\DateTime::createFromFormat("m/d/Y", $to));
            }
            $doct->flush();
        }
        return $this->redirectToRoute('booking', [
        ]);
    }

    /**
     * @Route("/booking/search", name="booking_search")
     */
    public function search(Request $request): Response
    {
        if (!$this->isAuth())
            return $this->redirectToRoute('connexion');
        $status = $request->request->get('status');
        $listing_id = $request->request->get('listing_id');
        $id = $request->request->get('id');
        $name = $request->request->get('name');
        $filter = [];
        if ($status != '0')
            $filter['status'] = $status;
        if ($listing_id != '0')
            $filter['listing_id'] = $listing_id;
        
        $doct = $this->getDoctrine()->getManager();
        $bookings = $doct->getRepository(Booking::class)->findWithFilter($filter);
        $listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
        $statusList = $this->getStatusList();
        foreach ($bookings as $booking) {
            $listing = $this->getDoctrine()->getRepository(Listing::class)->find($booking->getListingId());
            $booking->list_name = $listing->getName();
        }
        return $this->render('pages/booking/index.html.twig', [
            'page' => 'booking',
            'subtitle' => 'Bookings',
            'bookings' => $bookings,
            'listings' => $listings,
            'statues' => $statusList,
            'filter' => $filter
        ]);
    }

    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
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
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
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
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
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
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
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
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
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
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
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
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $doct = $this->getDoctrine()->getManager();
        $booking = $doct->getRepository(Booking::class)->find($id);
        $doct->remove($booking);
        $doct->flush();
        return $this->redirectToRoute('admin_booking', [
            'id' => $booking->getId()
        ]);
    }
}