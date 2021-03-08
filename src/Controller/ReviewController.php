<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\PropertyAccess\PropertyAccess;
use App\Entity\Review;
use App\Entity\Listing;
use \DateTime;

class ReviewController extends AbstractController
{
    /**
     * @Route("/review", name="review")
     */
    public function index(): Response
    {
        $reviews = $this->getDoctrine()->getRepository(Review::class)->findAll();
        foreach ($reviews as $review) {
            $listing = $this->getDoctrine()->getRepository(Listing::class)->find($review->getListingId());
            $review->list_name = $listing->getName();
        }
        return $this->render('pages/review/index.html.twig', [
            'page' => 'review',
            'subtitle' => 'Reviews',
            'reviews' => $reviews
        ]);
    }

    /**
     * @Route("/review/create", name="review_create")
     */
    public function create(): Response
    {
        return $this->render('pages/review/create.html.twig', [
            
        ]);
    }

    /**
     * @Route("/review/store", name="review_store")
     */
    public function store(Request $request, ValidatorInterface $validator): Response
    {
        $rate = $request->request->get("rate");
        $description = $request->request->get("description");
        $listing_id = $request->request->get('listing_id');
        $input = [
            'rate' => $rate,
            'description' => $description
        ];
        $constraints = new Assert\Collection([
            'rate' => [new Assert\NotBlank, new Assert\Range(['min' => 0, 'max' => 5, 'notInRangeMessage' => 'You must be between {{ min }} and {{ max }} to enter',])],
            'description' => [new Assert\NotBlank],
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

        $review = new Review();
        $user_id = $request->request->get('user_id');
        $review->setUserId($user_id);
        $rate = $request->request->get('rate');
        $review->setRate($rate);
        $description = $request->request->get('description');
        $review->setDescription($description);
        $listing_id = $request->request->get('listing_id');
        $review->setListingId($listing_id);
        $date = new DateTime();
        $review->setDate($date);
        
        // validate
        $errors = $validator->validate($review);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }
        // save
        $doct = $this->getDoctrine()->getManager();
        $doct->persist($review);
        $doct->flush();
        return $this->redirectToRoute('listing_detail', ['id'=> $listing_id]);
    }

    /**
     * @Route("/review/edit/{id}", name="review_edit")
     */
    public function edit($id): Response
    {
        $review = $this->getDoctrine()->getRepository(Review::class)->find($id);
        return $this->render('pages/review/edit.html.twig', [
            'review' => $review
        ]);
    }

    /**
     * @Route("/review/update/{id}", name="review_update")
     */
    public function update($id, Request $request, ValidatorInterface $validator): Response
    {
        $doct = $this->getDoctrine()->getManager();
        $review = $doct->getRepository(Review::class)->find($id);
        $adult_number = $request->request->get('adult_number');
        $review->setAdultNumber($adult_number);
        $children_number = $request->request->get('children_number');
        $review->setChildrenNumber($children_number);
        $listing_id = $request->request->get('listing_id');
        $review->setListingId($listing_id);
        $time = $request->request->get('time');
        $review->setTime($time);
        
        // validate
        $errors = $validator->validate($review);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }
        // update
        $doct->flush();
        return $this->redirectToRoute('review', [
            'id' => $review->getId()
        ]);
    }

    /**
     * @Route("/review/delete/{id}", name="review_delete")
     */
    public function delete($id): Response
    {
        $doct = $this->getDoctrine()->getManager();
        $review = $doct->getRepository(Review::class)->find($id);
        $doct->remove($review);
        $doct->flush();
        return $this->redirectToRoute('review', [
            'id' => $review->getId()
        ]);
    }

    /**
     * @Route("/admin/review", name="admin_review")
     */
    public function admin_index(): Response
    {
        $reviews = $this->getDoctrine()->getRepository(Review::class)->findAll();
        foreach ($reviews as $review) {
            $listing = $this->getDoctrine()->getRepository(Listing::class)->find($review->getListingId());
            $review->list_name = $listing->getName();
        }
        return $this->render('pages/admin/review/index.html.twig', [
            'reviews' => $reviews
        ]);
    }

    /**
     * @Route("/admin/review/create", name="admin_review_create")
     */
    public function admin_create(): Response
    {
        $listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
        return $this->render('pages/admin/review/create.html.twig', [
            'listings' => $listings
        ]);
    }

    /**
     * @Route("/admin/review/store", name="admin_review_store")
     */
    public function admin_store(Request $request, ValidatorInterface $validator): Response
    {
        $rate = $request->request->get("rate");
        $description = $request->request->get("description");
        $input = [
            'rate' => $rate,
            'description' => $description
        ];
        $constraints = new Assert\Collection([
            'rate' => [new Assert\NotBlank, new Assert\Range(['min' => 0, 'max' => 5, 'notInRangeMessage' => 'You must be between {{ min }} and {{ max }} to enter',])],
            'description' => [new Assert\NotBlank],
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
            return $this->render('pages/admin/review/create.html.twig', [
                'listings' => $listings,
                'errors' => $errorMessages,
                'old' => $input
            ]);
        }

        $review = new Review();
        $user_id = $request->request->get('user_id');
        $review->setUserId($user_id);
        $rate = $request->request->get('rate');
        $review->setRate($rate);
        $description = $request->request->get('description');
        $review->setDescription($description);
        $listing_id = $request->request->get('listing_id');
        $review->setListingId($listing_id);
        $date = new DateTime();
        $review->setDate($date);
        
        // validate
        $errors = $validator->validate($review);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }
        // save
        $doct = $this->getDoctrine()->getManager();
        $doct->persist($review);
        $doct->flush();
        return $this->redirectToRoute('admin_review', ['id'=> $listing_id]);
    }

    /**
     * @Route("/admin/review/edit/{id}", name="admin_review_edit")
     */
    public function admin_edit($id): Response
    {
        $review = $this->getDoctrine()->getRepository(Review::class)->find($id);
        $listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
        return $this->render('pages/admin/review/edit.html.twig', [
            'review' => $review,
            'listings' => $listings
        ]);
    }

    /**
     * @Route("/admin/review/update/{id}", name="admin_review_update")
     */
    public function admin_update($id, Request $request, ValidatorInterface $validator): Response
    {
        $rate = $request->request->get("rate");
        $description = $request->request->get("description");
        $input = [
            'rate' => $rate,
            'description' => $description
        ];
        $constraints = new Assert\Collection([
            'rate' => [new Assert\NotBlank, new Assert\Range(['min' => 0, 'max' => 5, 'notInRangeMessage' => 'You must be between {{ min }} and {{ max }} to enter',])],
            'description' => [new Assert\NotBlank],
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
            $review = $this->getDoctrine()->getRepository(Review::class)->find($id);
            $listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
            return $this->render('pages/admin/review/create.html.twig', [
                'review' => $review,
                'listings' => $listings,
                'errors' => $errorMessages,
                'old' => $input
            ]);
        }

        $doct = $this->getDoctrine()->getManager();
        $review = $doct->getRepository(Review::class)->find($id);
        $user_id = $request->request->get('user_id');
        $review->setUserId($user_id);
        $rate = $request->request->get('rate');
        $review->setRate($rate);
        $description = $request->request->get('description');
        $review->setDescription($description);
        $listing_id = $request->request->get('listing_id');
        $review->setListingId($listing_id);
        // $date = new DateTime();
        // $review->setDate($date);
        
        // validate
        $errors = $validator->validate($review);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }
        // update
        $doct->flush();
        return $this->redirectToRoute('admin_review', [
            'id' => $review->getId()
        ]);
    }

    /**
     * @Route("/admin/review/delete/{id}", name="admin_review_delete")
     */
    public function admin_delete($id): Response
    {
        $doct = $this->getDoctrine()->getManager();
        $review = $doct->getRepository(Review::class)->find($id);
        $doct->remove($review);
        $doct->flush();
        return $this->redirectToRoute('admin_review', [
            'id' => $review->getId()
        ]);
    }
}