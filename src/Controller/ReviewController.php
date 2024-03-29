<?php
namespace App\Controller;
   
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\PropertyAccess\PropertyAccess;
use App\Entity\Review;
use App\Entity\Listing;
use App\Entity\User;
use \DateTime;

class ReviewController extends AbstractController
{
    protected $session;
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
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
     * @Route("/review", name="review")
     */
    public function index(): Response
    {
        if (!$this->isAuth())
            return $this->redirectToRoute('connexion');
        if ($this->session->get('user')->getType() == 'client')
            return $this->redirectToRoute('dashboard');
        $listings = $this->getDoctrine()->getRepository(Listing::class)->findWithUserId($this->session->get('user')->getId());
        $reviews = [];
        foreach ($listings as $listing) {
            $tmp = $this->getDoctrine()->getRepository(Review::class)->findAllWithListingId($listing->getId());
            foreach ($tmp as $r) {
                $r->list_name = $listing->getName();
            }
            $reviews = array_merge($reviews, $tmp);
        }
        $rates = [1, 2, 3, 4, 5];
        return $this->render('pages/review/index.html.twig', [
            'page' => 'review',
            'subtitle' => 'Reviews',
            'reviews' => $reviews,
            'listings' => $listings,
            'rates' => $rates
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
        // echo $rate;
        // return false;
        $description = $request->request->get("description");
        $user_name = $request->request->get("user_name");
        $listing_id = $request->request->get('listing_id');
        $input = [
            'rate' => $rate,
            'description' => $description,
            'user_name' => $user_name
        ];
        $constraints = new Assert\Collection([
            'rate' => [new Assert\NotBlank, new Assert\Range(['min' => 0, 'max' => 5, 'notInRangeMessage' => 'You must be between {{ min }} and {{ max }} to enter',])],
            'description' => [new Assert\NotBlank],
            'user_name' => [new Assert\NotBlank],
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
            return $this->redirectToRoute('listing_detail', [
                'id' => $listing_id,
            ]);
        }

        $review = new Review();
        $user_name = $request->request->get('user_name');
        $review->setUserName($user_name);
        $rate = $request->request->get('rate');
        $review->setRate($rate);
        $description = $request->request->get('description');
        $review->setDescription($description);
        $listing_id = $request->request->get('listing_id');
        $review->setListingId($listing_id);
        $date = new DateTime();
        $review->setDate($date);
        $review->setFeature(false);

        $user_avatar_file = $request->files->get('user_avatar');
        if ($user_avatar_file) {
            $originalFilename = pathinfo($user_avatar_file->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $this->generateRandomString();
            $newFilename = $safeFilename.'.'.$user_avatar_file->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $user_avatar_file->move(
                    'upload/avatars/',
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            // updates the 'brochureFilename' property to store the PDF file name
            // instead of its contents
            $review->setUserAvatar('upload/avatars/'.$newFilename);
        }
        else {
            $review->setUserAvatar('assets/images/avatars/default.jpg');
        }
        
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
     * @Route("/review/search", name="review_search")
     */
    public function search(Request $request): Response
    {
        $listing_id = $request->request->get('listing_id');
        $rate = $request->request->get('rate');
        $user_name = $request->request->get('user_name');
        $filter = [];
        if ($listing_id != '0')
            $filter['listing_id'] = $listing_id;
        if ($rate != '0')
            $filter['rate'] = $rate;
        if ($user_name != '')
            $filter['user_name'] = $user_name;
        
        $doct = $this->getDoctrine()->getManager();
        $listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
        $rates = [1, 2, 3, 4, 5];
        $reviews = $doct->getRepository(Review::class)->findWithFilter($filter);
        foreach ($reviews as $review) {
            $listing = $this->getDoctrine()->getRepository(Listing::class)->find($review->getListingId());
            $review->list_name = $listing->getName();
        }
        return $this->render('pages/review/index.html.twig', [
            'page' => 'Review',
            'subtitle' => 'My Reviews',
            'listings' => $listings,
            'filter' => $filter,
            'reviews' => $reviews,
            'rates' => $rates,
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

    /**
     * @Route("/admin/review", name="admin_review")
     */
    public function admin_index(): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $reviews = $this->getDoctrine()->getRepository(Review::class)->findAll();
        $listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
        $rates = [1, 2, 3, 4, 5];
        foreach ($reviews as $review) {
            $listing = $this->getDoctrine()->getRepository(Listing::class)->find($review->getListingId());
            $review->list_name = $listing->getName();
        }
        return $this->render('pages/admin/review/index.html.twig', [
            'reviews' => $reviews,
            'listings' => $listings,
            'rates' => $rates
        ]);
    }

    /**
     * @Route("/admin/review/create", name="admin_review_create")
     */
    public function admin_create(): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
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
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $rate = $request->request->get("rate");
        $description = $request->request->get("description");
        $feature = $request->request->get('feature');
        $input = [
            'rate' => $rate,
            'description' => $description,
            'feature' => $feature
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
        $rate = $request->request->get('rate');
        $review->setRate($rate);
        $description = $request->request->get('description');
        $review->setDescription($description);
        $listing_id = $request->request->get('listing_id');
        $review->setListingId($listing_id);
        $date = new DateTime();
        $review->setDate($date);
        $feature = $request->request->get('feature');
        $review->setFeature($feature=='true');
        
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
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
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
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
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
        $rate = $request->request->get('rate');
        $review->setRate($rate);
        $description = $request->request->get('description');
        $review->setDescription($description);
        $listing_id = $request->request->get('listing_id');
        $review->setListingId($listing_id);
        $feature = $request->request->get('feature');
        $review->setFeature($feature=='true');
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
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $doct = $this->getDoctrine()->getManager();
        $review = $doct->getRepository(Review::class)->find($id);
        $doct->remove($review);
        $doct->flush();
        return $this->redirectToRoute('admin_review', [
            'id' => $review->getId()
        ]);
    }

    /**
     * @Route("/admin/review/search", name="admin_review_search")
     */
    public function admin_search(Request $request): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $listing_id = $request->request->get('listing_id');
        $rate = $request->request->get('rate');
        $user_name = $request->request->get('user_name');
        $filter = [];
        if ($listing_id != '0')
            $filter['listing_id'] = $listing_id;
        if ($rate != '0')
            $filter['rate'] = $rate;
        if ($user_name != '')
            $filter['user_name'] = $user_name;
        
        $doct = $this->getDoctrine()->getManager();
        $listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
        $rates = [1, 2, 3, 4, 5];
        $reviews = $doct->getRepository(Review::class)->findWithFilter($filter);
        foreach ($reviews as $review) {
            $listing = $this->getDoctrine()->getRepository(Listing::class)->find($review->getListingId());
            $review->list_name = $listing->getName();
        }
        return $this->render('pages/admin/review/index.html.twig', [
            'listings' => $listings,
            'filter' => $filter,
            'reviews' => $reviews,
            'rates' => $rates,
        ]);
    }
}