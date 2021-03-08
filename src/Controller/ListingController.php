<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\PropertyAccess\PropertyAccess;
use App\Entity\Listing;
use App\Entity\Review;

class ListingController extends AbstractController
{
    /**
     * @Route("/listing", name="listing")
     */
    public function index(): Response
    {
        $listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
        return $this->render('pages/listing/index.html.twig', [
            'page' => 'listing',
            'subtitle' => 'My Listings',
            'listings' => $listings
        ]);
    }

    /**
     * @Route("/listing/create", name="listing_create")
     */
    public function create(): Response
    {
        return $this->render('pages/listing/create.html.twig', [
            
        ]);
    }

    /**
     * @Route("/listing/store", name="listing_store")
     */
    public function store(Request $request, ValidatorInterface $validator): Response
    {
        $name = $request->request->get("name");
        $description = $request->request->get("description");
        $address = $request->request->get("address");
        $email = $request->request->get("email");
        $input = [
            'name' => $name,
            'description' => $description,
            'address' => $address,
            'email' => $email
        ];
        $constraints = new Assert\Collection([
            'name' => [new Assert\NotBlank],
            'description' => [new Assert\NotBlank],
            'address' => [new Assert\NotBlank],
            'email' => [new Assert\NotBlank, new Assert\Email()],
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
            return $this->render('pages/listing/create.html.twig', [
                'listings' => $listings,
                'errors' => $errorMessages,
                'old' => $input
            ]);
        }

        $listing = new Listing();
        $name = $request->request->get('name');
        $listing->setName($name);
        $price_range = $request->request->get('price_range');
        $listing->setPriceRange($price_range);
        $description = $request->request->get('description');
        $listing->setDescription($description);
        $category = $request->request->get('category');
        $listing->setCategory($category);
        $place_type = $request->request->get('place_type');
        $listing->setPlaceType($place_type);
        $city = $request->request->get('city');
        $listing->setCity($city);
        $listing->setStatus("pending");
        $wifi = $request->request->get('wifi');
        $listing->setWifi($wifi=='true');
        $parking = $request->request->get('parking');
        $listing->setParking($parking=='true');
        $accept_card = $request->request->get('accept_card');
        $listing->setAcceptCard($accept_card=='true');
        $garden = $request->request->get('garden');
        $listing->setGarden($garden=='true');
        $terrace = $request->request->get('terrace');
        $listing->setTerrace($terrace=='true');
        $toilet = $request->request->get('toilet');
        $listing->setToilet($toilet=='true');
        $air_conditioner = $request->request->get('air_conditioner');
        $listing->setAirConditioner($air_conditioner=='true');
        $airport_taxi = $request->request->get('airport_taxi');
        $listing->setAirportTaxi($airport_taxi=='true');
        $address = $request->request->get('address');
        $listing->setAddress($address);
        $email = $request->request->get('email');
        $listing->setEmail($email);
        $phone = $request->request->get('phone');
        $listing->setPhone($phone);
        $website = $request->request->get('website');
        $listing->setWebsite($website);
        $facebook = $request->request->get('facebook');
        $listing->setFacebook($facebook=='true');
        $instagram = $request->request->get('instagram');
        $listing->setInstagram($instagram=='true');
        $youtube = $request->request->get('youtube');
        $listing->setYoutube($youtube=='true');
        $twitter = $request->request->get('twitter');
        $listing->setTwitter($twitter=='true');
        $google = $request->request->get('google');
        $listing->setGoogle($google=='true');
        $pinterest = $request->request->get('pinterest');
        $listing->setPinterest($pinterest=='true');
        $snapchat = $request->request->get('snapchat');
        $listing->setSnapchat($snapchat=='true');
        $facebook_url = $request->request->get('facebook_url');
        $listing->setFacebookUrl($facebook_url);
        $instagram_url = $request->request->get('instagram_url');
        $listing->setInstagramUrl($instagram_url);
        $youtube_url = $request->request->get('youtube_url');
        $listing->setYoutubeUrl($youtube_url);
        $twitter_url = $request->request->get('twitter_url');
        $listing->setTwitterUrl($twitter_url);
        $google_url = $request->request->get('google_url');
        $listing->setGoogleUrl($google_url);
        $pinterest_url = $request->request->get('pinterest_url');
        $listing->setPinterestUrl($pinterest_url);
        $snapchat_url = $request->request->get('snapchat_url');
        $listing->setSnapchatUrl($snapchat_url);
        $monday = $request->request->get('monday');
        $listing->setMonday($monday=='true');
        $tuesday = $request->request->get('tuesday');
        $listing->setTuesday($tuesday=='true');
        $wednesday = $request->request->get('wednesday');
        $listing->setWednesday($wednesday=='true');
        $thursday = $request->request->get('thursday');
        $listing->setThursday($thursday=='true');
        $friday = $request->request->get('friday');
        $listing->setFriday($friday=='true');
        $saturday = $request->request->get('saturday');
        $listing->setSaturday($saturday=='true');
        $sunday = $request->request->get('sunday');
        $listing->setSunday($sunday=='true');
        $monday_start_time = $request->request->get('monday_start_time');
        $listing->setMondayStartTime($monday_start_time);
        $monday_end_time = $request->request->get('monday_end_time');
        $listing->setMondayEndTime($monday_end_time);
        $tuesday_start_time = $request->request->get('tuesday_start_time');
        $listing->setTuesdayStartTime($tuesday_start_time);
        $tuesday_end_time = $request->request->get('tuesday_end_time');
        $listing->setTuesdayEndTime($tuesday_end_time);
        $wednesday_start_time = $request->request->get('wednesday_start_time');
        $listing->setWednesdayStartTime($wednesday_start_time);
        $wednesday_end_time = $request->request->get('wednesday_end_time');
        $listing->setWednesdayEndTime($wednesday_end_time);
        $thurday_start_time = $request->request->get('thurday_start_time');
        $listing->setThursdayStartTime($thurday_start_time);
        $thursday_end_time = $request->request->get('thursday_end_time');
        $listing->setThursdayEndTime($thursday_end_time);
        $friday_start_time = $request->request->get('friday_start_time');
        $listing->setFridayStartTime($friday_start_time);
        $friday_end_time = $request->request->get('friday_end_time');
        $listing->setFridayEndTime($friday_end_time);
        $saturday_start_time = $request->request->get('saturday_start_time');
        $listing->setSaturdayStartTime($saturday_start_time);
        $saturday_end_time = $request->request->get('saturday_end_time');
        $listing->setSaturdayEndTime($saturday_end_time);
        $sunday_start_time = $request->request->get('sunday_start_time');
        $listing->setSundayStartTime($sunday_start_time);
        $sunday_end_time = $request->request->get('sunday_end_time');
        $listing->setSundayEndTime($sunday_end_time);
        $cover_image = $request->request->get('cover_image');
        $listing->setCoverImage($cover_image);
        $gallery_image = $request->request->get('gallery_image');
        $listing->setGalleryImage($gallery_image);
        $video = $request->request->get('video');
        $listing->setVideo($video);
        
        $cover_image_file = $request->files->get('cover_image');
        if ($cover_image_file) {
            $originalFilename = pathinfo($cover_image_file->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $this->generateRandomString();
            $newFilename = $safeFilename.'.'.$cover_image_file->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $cover_image_file->move(
                    'upload/images/',
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            // updates the 'brochureFilename' property to store the PDF file name
            // instead of its contents
            $listing->setCoverImage('upload/images/'.$newFilename);
        }
        $gallery_image_file = $request->files->get('gallery_image');
        if ($gallery_image_file) {
            $originalFilename = pathinfo($gallery_image_file->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $this->generateRandomString();
            $newFilename = $safeFilename.'.'.$gallery_image_file->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $gallery_image_file->move(
                    'upload/images/',
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            // updates the 'brochureFilename' property to store the PDF file name
            // instead of its contents
            $listing->setGalleryImage('upload/images/'.$newFilename);
        }
        
        // validate
        $errors = $validator->validate($listing);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }
        // save
        $doct = $this->getDoctrine()->getManager();
        $doct->persist($listing);
        $doct->flush();
        return $this->redirectToRoute('listing');
    }

    /**
     * @Route("/listing/edit/{id}", name="listing_edit")
     */
    public function edit($id): Response
    {
        $listing = $this->getDoctrine()->getRepository(Listing::class)->find($id);
        return $this->render('pages/listing/edit.html.twig', [
            'listing' => $listing
        ]);
    }

    /**
     * @Route("/listing/update/{id}", name="listing_update")
     */
    public function update($id, Request $request, ValidatorInterface $validator): Response
    {
        $name = $request->request->get("name");
        $description = $request->request->get("description");
        $address = $request->request->get("address");
        $email = $request->request->get("email");
        $input = [
            'name' => $name,
            'description' => $description,
            'address' => $address,
            'email' => $email
        ];
        $constraints = new Assert\Collection([
            'name' => [new Assert\NotBlank],
            'description' => [new Assert\NotBlank],
            'address' => [new Assert\NotBlank],
            'email' => [new Assert\NotBlank, new Assert\Email()],
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
            $listing = $this->getDoctrine()->getRepository(Listing::class)->find($id);
            return $this->render('pages/listing/edit.html.twig', [
                'listing' => $listing,
                'errors' => $errorMessages,
                'old' => $input
            ]);
        }

        $doct = $this->getDoctrine()->getManager();
        $listing = $doct->getRepository(Listing::class)->find($id);
        $name = $request->request->get('name');
        $listing->setName($name);
        $price_range = $request->request->get('price_range');
        $listing->setPriceRange($price_range);
        $description = $request->request->get('description');
        $listing->setDescription($description);
        $category = $request->request->get('category');
        $listing->setCategory($category);
        $place_type = $request->request->get('place_type');
        $listing->setPlaceType($place_type);
        $city = $request->request->get('city');
        $listing->setCity($city);
        $listing->setStatus("approved");
        $wifi = $request->request->get('wifi');
        $listing->setWifi($wifi=='true');
        $parking = $request->request->get('parking');
        $listing->setParking($parking=='true');
        $accept_card = $request->request->get('accept_card');
        $listing->setAcceptCard($accept_card=='true');
        $garden = $request->request->get('garden');
        $listing->setGarden($garden=='true');
        $terrace = $request->request->get('terrace');
        $listing->setTerrace($terrace=='true');
        $toilet = $request->request->get('toilet');
        $listing->setToilet($toilet=='true');
        $air_conditioner = $request->request->get('air_conditioner');
        $listing->setAirConditioner($air_conditioner=='true');
        $airport_taxi = $request->request->get('airport_taxi');
        $listing->setAirportTaxi($airport_taxi=='true');
        $address = $request->request->get('address');
        $listing->setAddress($address);
        $email = $request->request->get('email');
        $listing->setEmail($email);
        $phone = $request->request->get('phone');
        $listing->setPhone($phone);
        $website = $request->request->get('website');
        $listing->setWebsite($website);
        $facebook = $request->request->get('facebook');
        $listing->setFacebook($facebook=='true');
        $instagram = $request->request->get('instagram');
        $listing->setInstagram($instagram=='true');
        $youtube = $request->request->get('youtube');
        $listing->setYoutube($youtube=='true');
        $twitter = $request->request->get('twitter');
        $listing->setTwitter($twitter=='true');
        $google = $request->request->get('google');
        $listing->setGoogle($google=='true');
        $pinterest = $request->request->get('pinterest');
        $listing->setPinterest($pinterest=='true');
        $snapchat = $request->request->get('snapchat');
        $listing->setSnapchat($snapchat=='true');
        $facebook_url = $request->request->get('facebook_url');
        $listing->setFacebookUrl($facebook_url);
        $instagram_url = $request->request->get('instagram_url');
        $listing->setInstagramUrl($instagram_url);
        $youtube_url = $request->request->get('youtube_url');
        $listing->setYoutubeUrl($youtube_url);
        $twitter_url = $request->request->get('twitter_url');
        $listing->setTwitterUrl($twitter_url);
        $google_url = $request->request->get('google_url');
        $listing->setGoogleUrl($google_url);
        $pinterest_url = $request->request->get('pinterest_url');
        $listing->setPinterestUrl($pinterest_url);
        $snapchat_url = $request->request->get('snapchat_url');
        $listing->setSnapchatUrl($snapchat_url);
        $monday = $request->request->get('monday');
        $listing->setMonday($monday=='true');
        $tuesday = $request->request->get('tuesday');
        $listing->setTuesday($tuesday=='true');
        $wednesday = $request->request->get('wednesday');
        $listing->setWednesday($wednesday=='true');
        $thursday = $request->request->get('thursday');
        $listing->setThursday($thursday=='true');
        $friday = $request->request->get('friday');
        $listing->setFriday($friday=='true');
        $saturday = $request->request->get('saturday');
        $listing->setSaturday($saturday=='true');
        $sunday = $request->request->get('sunday');
        $listing->setSunday($sunday=='true');
        $monday_start_time = $request->request->get('monday_start_time');
        $listing->setMondayStartTime($monday_start_time);
        $monday_end_time = $request->request->get('monday_end_time');
        $listing->setMondayEndTime($monday_end_time);
        $tuesday_start_time = $request->request->get('tuesday_start_time');
        $listing->setTuesdayStartTime($tuesday_start_time);
        $tuesday_end_time = $request->request->get('tuesday_end_time');
        $listing->setTuesdayEndTime($tuesday_end_time);
        $wednesday_start_time = $request->request->get('wednesday_start_time');
        $listing->setWednesdayStartTime($wednesday_start_time);
        $wednesday_end_time = $request->request->get('wednesday_end_time');
        $listing->setWednesdayEndTime($wednesday_end_time);
        $thurday_start_time = $request->request->get('thurday_start_time');
        $listing->setThursdayStartTime($thurday_start_time);
        $thursday_end_time = $request->request->get('thursday_end_time');
        $listing->setThursdayEndTime($thursday_end_time);
        $friday_start_time = $request->request->get('friday_start_time');
        $listing->setFridayStartTime($friday_start_time);
        $friday_end_time = $request->request->get('friday_end_time');
        $listing->setFridayEndTime($friday_end_time);
        $saturday_start_time = $request->request->get('saturday_start_time');
        $listing->setSaturdayStartTime($saturday_start_time);
        $saturday_end_time = $request->request->get('saturday_end_time');
        $listing->setSaturdayEndTime($saturday_end_time);
        $sunday_start_time = $request->request->get('sunday_start_time');
        $listing->setSundayStartTime($sunday_start_time);
        $sunday_end_time = $request->request->get('sunday_end_time');
        $listing->setSundayEndTime($sunday_end_time);
        $cover_image = $request->request->get('cover_image');
        $listing->setCoverImage($cover_image);
        $gallery_image = $request->request->get('gallery_image');
        $listing->setGalleryImage($gallery_image);
        $video = $request->request->get('video');
        $listing->setVideo($video);

        $cover_image_file = $request->files->get('cover_image');
        if ($cover_image_file) {
            $originalFilename = pathinfo($cover_image_file->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $this->generateRandomString();
            $newFilename = $safeFilename.'.'.$cover_image_file->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $cover_image_file->move(
                    'upload/images/',
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            // updates the 'brochureFilename' property to store the PDF file name
            // instead of its contents
            $listing->setCoverImage('upload/images/'.$newFilename);
        }
        $gallery_image_file = $request->files->get('gallery_image');
        if ($gallery_image_file) {
            $originalFilename = pathinfo($gallery_image_file->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $this->generateRandomString();
            $newFilename = $safeFilename.'.'.$gallery_image_file->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $gallery_image_file->move(
                    'upload/images/',
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            // updates the 'brochureFilename' property to store the PDF file name
            // instead of its contents
            $listing->setGalleryImage('upload/images/'.$newFilename);
        }
        
        // validate
        $errors = $validator->validate($listing);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }
        // update
        $doct->flush();
        return $this->redirectToRoute('listing', [
            'id' => $listing->getId()
        ]);
    }

    /**
     * @Route("/listing/delete/{id}", name="listing_delete")
     */
    public function delete($id): Response
    {
        $doct = $this->getDoctrine()->getManager();
        $listing = $doct->getRepository(Listing::class)->find($id);
        $doct->remove($listing);
        $doct->flush();
        return $this->redirectToRoute('listing', [
            'id' => $listing->getId()
        ]);
    }

    /**
     * @Route("/listing/detail/{id}", name="listing_detail")
     */
    public function detail($id): Response
    {
        $listing = $this->getDoctrine()->getRepository(Listing::class)->find($id);
        $reviews = $this->getDoctrine()->getRepository(Review::class)->findAllWithListingId($listing->getId());
        return $this->render('pages/listing/detail.html.twig', [
            'listing' => $listing,
            'reviews' => $reviews
        ]);
    }

    /**
     * @Route("/listing/status/{id}", name="listing_status")
     */
    public function setStatus($id, Request $request, ValidatorInterface $validator): Response
    {
        $doct = $this->getDoctrine()->getManager();
        $listing = $doct->getRepository(Listing::class)->find($id);
        $status = $request->request->get('status');
        $listing->setStatus($status);
        
        // validate
        $errors = $validator->validate($listing);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }
        // update
        $doct->flush();
        return $this->redirectToRoute('admin_listing', [
            'id' => $listing->getId()
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

    /**
     * @Route("/admin/listing", name="admin_listing")
     */
    public function admin_index(): Response
    {
        $listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
        return $this->render('pages/admin/listing/index.html.twig', [
            'listings' => $listings
        ]);
    }

    /**
     * @Route("/admin/listing/edit/{id}", name="admin_listing_edit")
     */
    public function admin_edit($id): Response
    {
        $listing = $this->getDoctrine()->getRepository(Listing::class)->find($id);
        $statuslist = $this->getStatusList();
        return $this->render('pages/admin/listing/edit.html.twig', [
            'listing' => $listing,
            'statuslist' => $statuslist
        ]);
    }

    /**
     * @Route("/admin/listing/delete/{id}", name="admin_listing_delete")
     */
    public function admin_delete($id): Response
    {
        $doct = $this->getDoctrine()->getManager();
        $listing = $doct->getRepository(Listing::class)->find($id);
        $doct->remove($listing);
        $doct->flush();
        return $this->redirectToRoute('admin_listing', [
            'id' => $listing->getId()
        ]);
    }
}