<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
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
            'subtitle' => 'Bookings',
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
        $review = $doct->getRepository(Booking::class)->find($id);
        $doct->remove($review);
        $doct->flush();
        return $this->redirectToRoute('review', [
            'id' => $review->getId()
        ]);
    }
}