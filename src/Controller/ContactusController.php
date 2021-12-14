<?php
namespace App\Controller;
      
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\PropertyAccess\PropertyAccess;
use App\Entity\Listing;
use App\Entity\UserRequest;

class ContactusController extends AbstractController
{
    /**
     * @Route("/contactus", name="contactus")
     */
    public function index(): Response
    {
        $listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
        return $this->render('pages/contactus/index.html.twig', [
            'listings' => $listings
        ]);
    }

    /**
     * @Route("/contactus/store", name="contactus_store")
     */
    public function store(Request $request, ValidatorInterface $validator): Response
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
            'listing_id' => $listing_id,
        ];
        $constraints = new Assert\Collection([
            'first_name' => [new Assert\NotBlank],
            'last_name' => [new Assert\NotBlank],
            'email' => [new Assert\NotBlank],
            'phone_number' => [new Assert\NotBlank],
            'message' => [new Assert\NotBlank],
            'listing_id' => [new Assert\NotBlank],
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
            return $this->render('pages/contactus/index.html.twig', [
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
        $avatar_file = $request->files->get('avatar');
        if ($avatar_file) {
            $originalFilename = pathinfo($avatar_file->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $this->generateRandomString();
            $newFilename = $safeFilename.'.'.$avatar_file->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $avatar_file->move(
                    'upload/images/',
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            // updates the 'brochureFilename' property to store the PDF file name
            // instead of its contents
            $user_request->setAvatar('upload/images/'.$newFilename);
        }
        else {
            $user_request->setAvatar('assets/images/avatars/default.jpg');
        }
        
        // save
        $doct = $this->getDoctrine()->getManager();
        $doct->persist($user_request);
        $doct->flush();
        return $this->redirectToRoute('contactus', ['id'=> $listing_id]);
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
}