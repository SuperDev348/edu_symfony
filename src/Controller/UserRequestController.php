<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\PropertyAccess\PropertyAccess;
use App\Entity\UserRequest;
use App\Entity\Listing;
use \DateTime;

class UserRequestController extends AbstractController
{
    /**
     * @Route("/request", name="request")
     */
    public function index(): Response
    {
        $requests_tmp = $this->getDoctrine()->getRepository(UserRequest::class)->findAll();
        $requests = [];
        foreach ($requests_tmp as $request) {
            if ($request->getListingId() != '0') {
                $listing = $this->getDoctrine()->getRepository(Listing::class)->find($request->getListingId());
                $request->list_name = $listing->getName();
                array_push($requests, $request);
            }
        }
        $listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
        return $this->render('pages/request/index.html.twig', [
            'page' => 'request',
            'subtitle' => 'My Inqueries',
            'requests' => $requests,
            'listings' => $listings
        ]);
    }

    // /**
    //  * @Route("/request/create", name="request_create")
    //  */
    // public function create(): Response
    // {
    //     $listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
    //     return $this->render('pages/request/create.html.twig', [
    //         'listings' => $listings
    //     ]);
    // }

    // /**
    //  * @Route("/request/store", name="request_store")
    //  */
    // public function store(Request $request, ValidatorInterface $validator): Response
    // {
    //     $first_name = $request->request->get("first_name");
    //     $last_name = $request->request->get("last_name");
    //     $email = $request->request->get("email");
    //     $phone_number = $request->request->get("phone_number");
    //     $message = $request->request->get("message");
    //     $listing_id = $request->request->get('listing_id');
    //     $input = [
    //         'first_name' => $first_name,
    //         'last_name' => $last_name,
    //         'email' => $email,
    //         'phone_number' => $phone_number,
    //         'message' => $message,
    //     ];
    //     $constraints = new Assert\Collection([
    //         'first_name' => [new Assert\NotBlank],
    //         'last_name' => [new Assert\NotBlank],
    //         'email' => [new Assert\NotBlank],
    //         'phone_number' => [new Assert\NotBlank],
    //         'message' => [new Assert\NotBlank],
    //     ]);
    //     $violations = $validator->validate($input, $constraints);
    //     if (count($violations) > 0) {
    //         $accessor = PropertyAccess::createPropertyAccessor();
    //         $errorMessages = [];
    //         foreach ($violations as $violation) {
    //             $accessor->setValue($errorMessages,
    //             $violation->getPropertyPath(),
    //             $violation->getMessage());
    //         }
    //         $listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
    //         return $this->render('pages/request/create.html.twig', [
    //             'listings' => $listings,
    //             'errors' => $errorMessages,
    //             'old' => $input
    //         ]);
    //     }

    //     $user_request = new UserRequest();
    //     $listing_id = $request->request->get('listing_id');
    //     $user_request->setListingId($listing_id);
    //     $first_name = $request->request->get('first_name');
    //     $user_request->setFirstName($first_name);
    //     $last_name = $request->request->get('last_name');
    //     $user_request->setLastName($last_name);
    //     $email = $request->request->get('email');
    //     $user_request->setEmail($email);
    //     $phone_number = $request->request->get('phone_number');
    //     $user_request->setPhoneNumber($phone_number);
    //     $message = $request->request->get('message');
    //     $user_request->setMessage($message);
        
    //     // save
    //     $doct = $this->getDoctrine()->getManager();
    //     $doct->persist($user_request);
    //     $doct->flush();
    //     return $this->redirectToRoute('request', ['id'=> $listing_id]);
    // }

    // /**
    //  * @Route("/request/edit/{id}", name="request_edit")
    //  */
    // public function edit($id): Response
    // {
    //     $user_request = $this->getDoctrine()->getRepository(UserRequest::class)->find($id);
    //     $listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
    //     return $this->render('pages/request/edit.html.twig', [
    //         'request' => $user_request,
    //         'listings' => $listings
    //     ]);
    // }

    // /**
    //  * @Route("/request/update/{id}", name="request_update")
    //  */
    // public function update($id, Request $request, ValidatorInterface $validator): Response
    // {
    //     $first_name = $request->request->get("first_name");
    //     $last_name = $request->request->get("last_name");
    //     $email = $request->request->get("email");
    //     $phone_number = $request->request->get("phone_number");
    //     $message = $request->request->get("message");
    //     $listing_id = $request->request->get('listing_id');
    //     $input = [
    //         'first_name' => $first_name,
    //         'last_name' => $last_name,
    //         'email' => $email,
    //         'phone_number' => $phone_number,
    //         'message' => $message,
    //     ];
    //     $constraints = new Assert\Collection([
    //         'first_name' => [new Assert\NotBlank],
    //         'last_name' => [new Assert\NotBlank],
    //         'email' => [new Assert\NotBlank],
    //         'phone_number' => [new Assert\NotBlank],
    //         'message' => [new Assert\NotBlank],
    //     ]);
    //     $violations = $validator->validate($input, $constraints);
    //     if (count($violations) > 0) {
    //         $accessor = PropertyAccess::createPropertyAccessor();
    //         $errorMessages = [];
    //         foreach ($violations as $violation) {
    //             $accessor->setValue($errorMessages,
    //             $violation->getPropertyPath(),
    //             $violation->getMessage());
    //         }
    //         $listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
    //         return $this->render('pages/admin/request/create.html.twig', [
    //             'listings' => $listings,
    //             'errors' => $errorMessages,
    //             'old' => $input
    //         ]);
    //     }

    //     $doct = $this->getDoctrine()->getManager();
    //     $user_request = $doct->getRepository(UserRequest::class)->find($id);
    //     $listing_id = $request->request->get('listing_id');
    //     $user_request->setListingId($listing_id);
    //     $first_name = $request->request->get('first_name');
    //     $user_request->setFirstName($first_name);
    //     $last_name = $request->request->get('last_name');
    //     $user_request->setLastName($last_name);
    //     $email = $request->request->get('email');
    //     $user_request->setEmail($email);
    //     $phone_number = $request->request->get('phone_number');
    //     $user_request->setPhoneNumber($phone_number);
    //     $message = $request->request->get('message');
    //     $user_request->setMessage($message);
        
    //     // update
    //     $doct->flush();
    //     return $this->redirectToRoute('request', [
    //         'id' => $user_request->getId()
    //     ]);
    // }

    /**
     * @Route("/request/delete/{id}", name="request_delete")
     */
    public function delete($id): Response
    {
        $doct = $this->getDoctrine()->getManager();
        $user_request = $doct->getRepository(UserRequest::class)->find($id);
        $doct->remove($user_request);
        $doct->flush();
        return $this->redirectToRoute('request', [
            'id' => $user_request->getId()
        ]);
    }

    /**
     * @Route("/request/search", name="request_search")
     */
    public function search(Request $request): Response
    {
        $listing_id = $request->request->get('listing_id');
        $name = $request->request->get('name');
        $filter = [];
        if ($listing_id != '0')
            $filter['listing_id'] = $listing_id;
        if ($name != '')
            $filter['name'] = $name;
        
        $doct = $this->getDoctrine()->getManager();
        $requests_tmp = $doct->getRepository(UserRequest::class)->findWithFilter($filter);
        $requests = [];
        foreach ($requests_tmp as $request) {
            if ($request->getListingId() != '0') {
                $listing = $this->getDoctrine()->getRepository(Listing::class)->find($request->getListingId());
                $request->list_name = $listing->getName();
                array_push($requests, $request);
            }
        }
        $listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
        return $this->render('pages/request/index.html.twig', [
            'page' => 'request',
            'subtitle' => 'My Inqueries',
            'requests' => $requests,
            'listings' => $listings,
            'filter' => $filter,
        ]);
    }

    /**
     * @Route("/admin/request", name="admin_request")
     */
    public function admin_index(): Response
    {
        $requests = $this->getDoctrine()->getRepository(UserRequest::class)->findAll();
        foreach ($requests as $request) {
            if ($request->getListingId() == '0')
                $request->list_name = "None";
            else {
                $listing = $this->getDoctrine()->getRepository(Listing::class)->find($request->getListingId());
                $request->list_name = $listing->getName();
            }
        }
        $listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
        return $this->render('pages/admin/request/index.html.twig', [
            'requests' => $requests,
            'listings' => $listings
        ]);
    }

    /**
     * @Route("/admin/request/create", name="admin_request_create")
     */
    public function admin_create(): Response
    {
        $listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
        return $this->render('pages/admin/request/create.html.twig', [
            'listings' => $listings
        ]);
    }

    /**
     * @Route("/admin/request/store", name="admin_request_store")
     */
    public function admin_store(Request $request, ValidatorInterface $validator): Response
    {
        $first_name = $request->request->get("first_name");
        $last_name = $request->request->get("last_name");
        $email = $request->request->get("email");
        $phone_number = $request->request->get("phone_number");
        $message = $request->request->get("message");
        $listing_id = $request->request->get('listing_id');
        $input = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'phone_number' => $phone_number,
            'message' => $message,
        ];
        $constraints = new Assert\Collection([
            'first_name' => [new Assert\NotBlank],
            'last_name' => [new Assert\NotBlank],
            'email' => [new Assert\NotBlank],
            'phone_number' => [new Assert\NotBlank],
            'message' => [new Assert\NotBlank],
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
            return $this->render('pages/admin/request/create.html.twig', [
                'listings' => $listings,
                'errors' => $errorMessages,
                'old' => $input
            ]);
        }

        $user_request = new UserRequest();
        $listing_id = $request->request->get('listing_id');
        $user_request->setListingId($listing_id);
        $first_name = $request->request->get('first_name');
        $user_request->setFirstName($first_name);
        $last_name = $request->request->get('last_name');
        $user_request->setLastName($last_name);
        $email = $request->request->get('email');
        $user_request->setEmail($email);
        $phone_number = $request->request->get('phone_number');
        $user_request->setPhoneNumber($phone_number);
        $message = $request->request->get('message');
        $user_request->setMessage($message);
        
        // save
        $doct = $this->getDoctrine()->getManager();
        $doct->persist($user_request);
        $doct->flush();
        return $this->redirectToRoute('admin_request', ['id'=> $listing_id]);
    }

    /**
     * @Route("/admin/request/edit/{id}", name="admin_request_edit")
     */
    public function admin_edit($id): Response
    {
        $user_request = $this->getDoctrine()->getRepository(UserRequest::class)->find($id);
        $listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
        return $this->render('pages/admin/request/edit.html.twig', [
            'request' => $user_request,
            'listings' => $listings
        ]);
    }

    /**
     * @Route("/admin/request/update/{id}", name="admin_request_update")
     */
    public function admin_update($id, Request $request, ValidatorInterface $validator): Response
    {
        $first_name = $request->request->get("first_name");
        $last_name = $request->request->get("last_name");
        $email = $request->request->get("email");
        $phone_number = $request->request->get("phone_number");
        $message = $request->request->get("message");
        $listing_id = $request->request->get('listing_id');
        $input = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'phone_number' => $phone_number,
            'message' => $message,
        ];
        $constraints = new Assert\Collection([
            'first_name' => [new Assert\NotBlank],
            'last_name' => [new Assert\NotBlank],
            'email' => [new Assert\NotBlank],
            'phone_number' => [new Assert\NotBlank],
            'message' => [new Assert\NotBlank],
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
            return $this->render('pages/admin/request/create.html.twig', [
                'listings' => $listings,
                'errors' => $errorMessages,
                'old' => $input
            ]);
        }

        $doct = $this->getDoctrine()->getManager();
        $user_request = $doct->getRepository(UserRequest::class)->find($id);
        $listing_id = $request->request->get('listing_id');
        $user_request->setListingId($listing_id);
        $first_name = $request->request->get('first_name');
        $user_request->setFirstName($first_name);
        $last_name = $request->request->get('last_name');
        $user_request->setLastName($last_name);
        $email = $request->request->get('email');
        $user_request->setEmail($email);
        $phone_number = $request->request->get('phone_number');
        $user_request->setPhoneNumber($phone_number);
        $message = $request->request->get('message');
        $user_request->setMessage($message);
        
        // update
        $doct->flush();
        return $this->redirectToRoute('admin_request', [
            'id' => $user_request->getId()
        ]);
    }

    /**
     * @Route("/admin/request/delete/{id}", name="admin_request_delete")
     */
    public function admin_delete($id): Response
    {
        $doct = $this->getDoctrine()->getManager();
        $user_request = $doct->getRepository(UserRequest::class)->find($id);
        $doct->remove($user_request);
        $doct->flush();
        return $this->redirectToRoute('admin_request', [
            'id' => $user_request->getId()
        ]);
    }

    /**
     * @Route("/admin/request/search", name="admin_request_search")
     */
    public function admin_search(Request $request): Response
    {
        $listing_id = $request->request->get('listing_id');
        $name = $request->request->get('name');
        $filter = [];
        if ($listing_id != '-1')
            $filter['listing_id'] = $listing_id;
        if ($name != '')
            $filter['name'] = $name;
        
        $doct = $this->getDoctrine()->getManager();
        $requests = $doct->getRepository(UserRequest::class)->findWithFilter($filter);
        $listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
        foreach ($requests as $request) {
            if ($request->getListingId() == '0')
                $request->list_name = "None";
            else {
                $listing = $this->getDoctrine()->getRepository(Listing::class)->find($request->getListingId());
                $request->list_name = $listing->getName();
            }
        }
        return $this->render('pages/admin/request/index.html.twig', [
            'requests' => $requests,
            'listings' => $listings,
            'filter' => $filter,
        ]);
    }
}