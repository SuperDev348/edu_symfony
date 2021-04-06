<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\PropertyAccess\PropertyAccess;
use App\Entity\Listing;
use App\Entity\Review;
use App\Entity\Setting;
use App\Entity\Suggestion;
use App\Entity\User;
use App\Entity\City;
use App\Entity\CategoryType;
use App\Entity\ActiveType;
use App\Entity\Wishlist;
use App\Entity\Message;
use \DateTime;

class ListingController extends AbstractController
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
     * @Route("/listing", name="listing")
     */
    public function index(): Response
    {
        if (!$this->isAuth())
            return $this->redirectToRoute('connexion');
        if ($this->session->get('user')->getType() == 'client')
            return $this->redirectToRoute('dashboard');
        $listings = $this->getDoctrine()->getRepository(Listing::class)->findWithUserId($this->session->get('user')->getId());
        foreach ($listings as $listing) {
            $listing->city = $this->getDoctrine()->getRepository(City::class)->find($listing->getCityId());
            $listing->category = $this->getDoctrine()->getRepository(CategoryType::class)->find($listing->getCategoryId());
        }
        $cities = $this->getDoctrine()->getRepository(City::class)->findAll();
        $categories = $this->getDoctrine()->getRepository(CategoryType::class)->findAll();
        return $this->render('pages/listing/index.html.twig', [
            'page' => 'listing',
            'subtitle' => 'My Listings',
            'listings' => $listings,
            'cities' => $cities,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/listing/create", name="listing_create")
     */
    public function create(): Response
    {
        if (!$this->isAuth())
            return $this->redirectToRoute('connexion');
        $cities = $this->getDoctrine()->getRepository(City::class)->findAll();
        $categories = $this->getDoctrine()->getRepository(CategoryType::class)->findAll();
        $placetypes = $this->getDoctrine()->getRepository(ActiveType::class)->findAll();
        return $this->render('pages/listing/create.html.twig', [
            'cities' => $cities,
            'categories' => $categories,
            'placetypes' => $placetypes
        ]);
    }

    private function listing_create(Request $request) {
        $listing = new Listing();
        $listing->setUserId($this->session->get('user')->getId());
        $name = $request->request->get('name');
        $listing->setName($name);
        $price_range = $request->request->get('price_range');
        $listing->setPriceRange($price_range);
        $description = $request->request->get('description');
        $listing->setDescription($description);
        $category_id = $request->request->get('category_id');
        $listing->setCategoryId($category_id);
        $place_type_id = $request->request->get('place_type_id');
        $listing->setPlaceTypeId($place_type_id);
        $city_id = $request->request->get('city_id');
        $listing->setCityId($city_id);
        $listing->setStatus("pending");
        $listing->setFeature(false);
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
        $La_g = $request->request->get('La_g');
        $listing->setLaG($La_g);
        $La_i = $request->request->get('La_i');
        $listing->setLaI($La_i);
        $Ra_g = $request->request->get('Ra_g');
        $listing->setRaG($Ra_g);
        $Ra_i = $request->request->get('Ra_i');
        $listing->setRaI($Ra_i);
        $lat = $request->request->get('lat');
        $listing->setLat($lat);
        $lng = $request->request->get('lng');
        $listing->setLng($lng);
        $googlemap_address = $request->request->get('googlemap_address');
        $listing->setGooglemapAddress($googlemap_address);
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
        $video = $request->request->get('video');
        $listing->setVideo($video);
        $listing->setVisitNumber(0);
        
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

        // save
        $doct = $this->getDoctrine()->getManager();
        $doct->persist($listing);
        // set the user's type businessowner
        $user = $doct->getRepository(User::class)->findOneBy([
            'mail' => $this->session->get('user')->getMail(),
        ]);
        if ($this->session->get('user')->getType() == 'client')
            $user->setType('businessowner');
        $this->session->set('user', $user);
        $doct->flush();
        $message = new Message();
        $date = new DateTime();
        $message->setDate($date);
        $message->setIsShow(true);
        $message->setListingId($listing->getId());
        $description = "New listing (" . $name . ") is created.";
        $message->setDescription($description);
        $doct->persist($message);
        $doct->flush();
    }

    /**
     * @Route("/listing/store", name="listing_store")
     */
    public function store(Request $request, ValidatorInterface $validator): Response
    {
        if (!$this->isAuth())
            return $this->redirectToRoute('connexion');
        $cities = $this->getDoctrine()->getRepository(City::class)->findAll();
        $categories = $this->getDoctrine()->getRepository(CategoryType::class)->findAll();
        $listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
        $placetypes = $this->getDoctrine()->getRepository(ActiveType::class)->findAll();

        $name = $request->request->get("name");
        $description = $request->request->get("description");
        $category_id = $request->request->get("category_id");
        $city_id = $request->request->get("city_id");
        $address = $request->request->get("address");
        $email = $request->request->get("email");
        $La_g = $request->request->get("La_g");
        $lat = $request->request->get("lat");
        $input = [
            'name' => $name,
            'description' => $description,
            'category_id' => $category_id,
            'city_id' => $city_id,
            'address' => $address,
            'email' => $email,
            'La_g' => $La_g,
            'lat' => $lat
        ];
        $constraints = new Assert\Collection([
            'name' => [new Assert\NotBlank],
            'description' => [new Assert\NotBlank],
            'category_id' => [new Assert\NotBlank],
            'city_id' => [new Assert\NotBlank],
            'address' => [new Assert\NotBlank],
            'email' => [new Assert\NotBlank, new Assert\Email()],
            'La_g' => [new Assert\NotBlank],
            'lat' => [new Assert\NotBlank],
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
            
            return $this->render('pages/listing/create.html.twig', [
                'listings' => $listings,
                'errors' => $errorMessages,
                'old' => $input,
                'categories' => $categories,
                'cities' => $cities,
                'placetypes' => $placetypes
            ]);
        }
        $cover_image_file = $request->files->get('cover_image');
        if (!$cover_image_file) {
            $errorMessages['cover_image'] = "Cover image is require.";
            return $this->render('pages/listing/create.html.twig', [
                'listings' => $listings,
                'errors' => $errorMessages,
                'old' => $input,
                'categories' => $categories,
                'cities' => $cities,
                'placetypes' => $placetypes
            ]);
        }
        $this->listing_create($request);
        return $this->redirectToRoute('listing');
    }

    /**
     * @Route("/listing/edit/{id}", name="listing_edit")
     */
    public function edit($id): Response
    {
        if (!$this->isAuth())
            return $this->redirectToRoute('connexion');
        $listing = $this->getDoctrine()->getRepository(Listing::class)->find($id);
        $cities = $this->getDoctrine()->getRepository(City::class)->findAll();
        $categories = $this->getDoctrine()->getRepository(CategoryType::class)->findAll();
        $placetypes = $this->getDoctrine()->getRepository(ActiveType::class)->findAll();
        return $this->render('pages/listing/edit.html.twig', [
            'listing' => $listing,
            'cities' => $cities,
            'categories' => $categories,
            'placetypes' => $placetypes
        ]);
    }

    private function listing_edit($id, Request $request) {
        $doct = $this->getDoctrine()->getManager();
        $listing = $doct->getRepository(Listing::class)->find($id);
        $name = $request->request->get('name');
        $listing->setName($name);
        $price_range = $request->request->get('price_range');
        $listing->setPriceRange($price_range);
        $description = $request->request->get('description');
        $listing->setDescription($description);
        $category_id = $request->request->get('category_id');
        $listing->setCategoryId($category_id);
        $place_type_id = $request->request->get('place_type_id');
        $listing->setPlaceTypeId($place_type_id);
        $city_id = $request->request->get('city_id');
        $listing->setCityId($city_id);
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
        $La_g = $request->request->get('La_g');
        $listing->setLaG($La_g);
        $La_i = $request->request->get('La_i');
        $listing->setLaI($La_i);
        $Ra_g = $request->request->get('Ra_g');
        $listing->setRaG($Ra_g);
        $Ra_i = $request->request->get('Ra_i');
        $listing->setRaI($Ra_i);
        $lat = $request->request->get('lat');
        $listing->setLat($lat);
        $lng = $request->request->get('lng');
        $listing->setLng($lng);
        $googlemap_address = $request->request->get('googlemap_address');
        $listing->setGooglemapAddress($googlemap_address);
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
        
        // update
        $doct->flush();
    }

    /**
     * @Route("/listing/update/{id}", name="listing_update")
     */
    public function update($id, Request $request, ValidatorInterface $validator): Response
    {
        if (!$this->isAuth())
            return $this->redirectToRoute('connexion');
        $name = $request->request->get("name");
        $description = $request->request->get("description");
        $category_id = $request->request->get("category_id");
        $city_id = $request->request->get("city_id");
        $address = $request->request->get("address");
        $email = $request->request->get("email");
        $La_g = $request->request->get("La_g");
        $lat = $request->request->get("lat");
        $input = [
            'name' => $name,
            'description' => $description,
            'category_id' => $category_id,
            'city_id' => $city_id,
            'address' => $address,
            'email' => $email,
            'La_g' => $La_g,
            'lat' => $lat
        ];
        $constraints = new Assert\Collection([
            'name' => [new Assert\NotBlank],
            'description' => [new Assert\NotBlank],
            'category_id' => [new Assert\NotBlank],
            'city_id' => [new Assert\NotBlank],
            'address' => [new Assert\NotBlank],
            'email' => [new Assert\NotBlank, new Assert\Email()],
            'La_g' => [new Assert\NotBlank],
            'lat' => [new Assert\NotBlank],
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
            $cities = $this->getDoctrine()->getRepository(City::class)->findAll();
            $categories = $this->getDoctrine()->getRepository(CategoryType::class)->findAll();
            $placetypes = $this->getDoctrine()->getRepository(ActiveType::class)->findAll();
            return $this->render('pages/listing/edit.html.twig', [
                'listing' => $listing,
                'cities' => $cities,
                'categories' => $categories,
                'placetypes' => $placetypes,
                'errors' => $errorMessages,
                'old' => $input
            ]);
        }
        $this->listing_edit($id, $request);
        
        return $this->redirectToRoute('listing', [
        ]);
    }

    /**
     * @Route("/listing/delete/{id}", name="listing_delete")
     */
    public function delete($id): Response
    {
        if (!$this->isAuth())
            return $this->redirectToRoute('connexion');
        $doct = $this->getDoctrine()->getManager();
        $listing = $doct->getRepository(Listing::class)->find($id);
        $doct->remove($listing);
        $doct->flush();
        return $this->redirectToRoute('listing', [
            'id' => $listing->getId()
        ]);
    }

    /**
     * @Route("/listing/search", name="listing_search")
     */
    public function search(Request $request): Response
    {
        if (!$this->isAuth())
            return $this->redirectToRoute('connexion');
        $category_id = $request->request->get('category_id');
        $city_id = $request->request->get('city_id');
        $id = $request->request->get('id');
        $name = $request->request->get('name');
        $filter = [];
        $filter['user_id'] = $this->session->get('user')->getId();
        if ($category_id != '0')
            $filter['category_id'] = $category_id;
        if ($city_id != '0')
            $filter['city_id'] = $city_id;
        if ($id != '')
            $filter['id'] = $id;
        if ($name != '')
            $filter['name'] = $name;
        
        $doct = $this->getDoctrine()->getManager();
        $listings = $doct->getRepository(Listing::class)->findWithFilter($filter);
        foreach ($listings as $listing) {
            $listing->city = $doct->getRepository(City::class)->find($listing->getCityId());
            $listing->category = $doct->getRepository(CategoryType::class)->find($listing->getCategoryId());
        }
        $cities = $this->getDoctrine()->getRepository(City::class)->findAll();
        $categories = $this->getDoctrine()->getRepository(CategoryType::class)->findAll();
        return $this->render('pages/listing/index.html.twig', [
            'page' => 'listing',
            'subtitle' => 'My Listings',
            'listings' => $listings,
            'filter' => $filter,
            'cities' => $cities,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/listing/all", name="listing_all")
     */
    public function all(): Response
    {
        $listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
        foreach ($listings as $listing) {
            $listing->city = $this->getDoctrine()->getRepository(City::class)->find($listing->getCityId());
            $listing->category = $this->getDoctrine()->getRepository(CategoryType::class)->find($listing->getCategoryId());
            $review = $this->review($listing);
            $listing->reviews = $review['reviews'];
            $listing->review_rate = $review['review_rate'];
            $listing->user = $this->getDoctrine()->getRepository(User::class)->find($listing->getUserId());
        }
        $cities = $this->getDoctrine()->getRepository(City::class)->findAll();
        $categories = $this->getDoctrine()->getRepository(CategoryType::class)->findAll();
        $place_types = $this->getDoctrine()->getRepository(ActiveType::class)->findAll();
        $price_ranges = ['$', '$$', '$$$', '$$$$'];
        return $this->render('pages/listing/all.html.twig', [
            'listings' => $listings,
            'cities' => $cities,
            'categories' => $categories,
            'place_types' => $place_types,
            'price_ranges' => $price_ranges
        ]);
    }

    /**
     * @Route("/listing/searchall", name="listing_searchall")
     */
    public function searchall(Request $request): Response
    {
        $category_id = $request->request->get('category_id');
        $city_id = $request->request->get('city_id');
        $place_type_id = $request->request->get('place_type_id');
        $price_range = $request->request->get('price_range');
        $filter = [];
        if ($category_id != '0')
            $filter['category_id'] = $category_id;
        if ($city_id != '0')
            $filter['city_id'] = $city_id;
        if ($place_type_id != '0')
            $filter['place_type_id'] = $place_type_id;
        if ($price_range != '0')
            $filter['price_range'] = $price_range;
        
        $doct = $this->getDoctrine()->getManager();
        $listings = $doct->getRepository(Listing::class)->findWithFilter($filter);
        foreach ($listings as $listing) {
            $listing->city = $this->getDoctrine()->getRepository(City::class)->find($listing->getCityId());
            $listing->category = $this->getDoctrine()->getRepository(CategoryType::class)->find($listing->getCategoryId());
            $review = $this->review($listing);
            $listing->reviews = $review['reviews'];
            $listing->review_rate = $review['review_rate'];
            $listing->user = $this->getDoctrine()->getRepository(User::class)->find($listing->getUserId());
        }
        $cities = $this->getDoctrine()->getRepository(City::class)->findAll();
        $categories = $this->getDoctrine()->getRepository(CategoryType::class)->findAll();
        $place_types = $this->getDoctrine()->getRepository(ActiveType::class)->findAll();
        $price_ranges = ['$', '$$', '$$$', '$$$$'];
        return $this->render('pages/listing/all.html.twig', [
            'listings' => $listings,
            'filter' => $filter,
            'cities' => $cities,
            'categories' => $categories,
            'place_types' => $place_types,
            'price_ranges' => $price_ranges
        ]);
    }

    /**
     * @Route("/listing/filter/{filter}/{id}", name="listing_filter")
     */
    public function filter($filter, $id): Response
    {
        $filter = [$filter => $id];
        
        $doct = $this->getDoctrine()->getManager();
        $listings = $doct->getRepository(Listing::class)->findWithFilter($filter);
        foreach ($listings as $listing) {
            $listing->city = $this->getDoctrine()->getRepository(City::class)->find($listing->getCityId());
            $listing->category = $this->getDoctrine()->getRepository(CategoryType::class)->find($listing->getCategoryId());
            $review = $this->review($listing);
            $listing->reviews = $review['reviews'];
            $listing->review_rate = $review['review_rate'];
            $listing->user = $this->getDoctrine()->getRepository(User::class)->find($listing->getUserId());
        }
        $cities = $this->getDoctrine()->getRepository(City::class)->findAll();
        $categories = $this->getDoctrine()->getRepository(CategoryType::class)->findAll();
        $place_types = $this->getDoctrine()->getRepository(ActiveType::class)->findAll();
        $price_ranges = ['$', '$$', '$$$', '$$$$'];
        return $this->render('pages/listing/all.html.twig', [
            'listings' => $listings,
            'filter' => $filter,
            'cities' => $cities,
            'categories' => $categories,
            'place_types' => $place_types,
            'price_ranges' => $price_ranges
        ]);
    }

    private function review($listing) {
        $reviews = $this->getDoctrine()->getRepository(Review::class)->findAllWithListingId($listing->getId());
        $review_rate = 0;
        if (count($reviews) != 0) {
            foreach($reviews as $review) {
                $review_rate = $review_rate + $review->getRate();
            }
            $review_rate = number_format($review_rate/count($reviews), 2);
        }
        $res = ['reviews' => $reviews, 'review_rate' => $review_rate];
        return $res;
    }

    /**
     * @Route("/listing/detail/{id}", name="listing_detail")
     */
    public function detail($id): Response
    {
        if (!$this->isAuth())
            return $this->redirectToRoute('connexion');
        $this->visit($id);
        $attached = false;
        $wishlists = $this->getDoctrine()->getRepository(Wishlist::class)->findWithUserId($this->session->get('user')->getId());
        $ids = [];
        foreach ($wishlists as $wishlist) {
            array_push($ids, $wishlist->getListingId());
        }
        if (in_array($id, $ids)) {
            $attached = true;
        }
        $listing = $this->getDoctrine()->getRepository(Listing::class)->find($id);
        $listing->category = $this->getDoctrine()->getRepository(CategoryType::class)->find($listing->getCategoryId());
        $listing->city = $this->getDoctrine()->getRepository(City::class)->find($listing->getCityId());
        $similar_listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
        $similar_listings = array_slice($similar_listings, 0, 4);
        foreach($similar_listings as $similar_listing) {
            $similar_listing->user = $this->getDoctrine()->getRepository(User::class)->find($similar_listing->getId());
            $similar_listing->category = $this->getDoctrine()->getRepository(CategoryType::class)->find($similar_listing->getCategoryId());
            $similar_listing->city = $this->getDoctrine()->getRepository(City::class)->find($similar_listing->getCityId());
            $similar_listing->user = $this->getDoctrine()->getRepository(User::class)->find($similar_listing->getUserId());
            $review = $this->review($similar_listing);
            $similar_listing->review_rate = $review['review_rate'];
            $similar_listing->review_count = count($review['reviews']);
        }
        $suggestions = $this->getDoctrine()->getRepository(Suggestion::class)->findAll();
        $review = $this->review($listing);
        $reviews = $review['reviews'];
        $review_rate = $review['review_rate'];
        return $this->render('pages/listing/detail.html.twig', [
            'listing' => $listing,
            'similar_listings' => $similar_listings,
            'reviews' => $reviews,
            'review_rate' => $review_rate,
            'suggestions' => $suggestions,
            'attached' => $attached
        ]);
    }

    /**
     * @Route("/listing/status/{id}", name="listing_status")
     */
    public function setStatus($id, Request $request, ValidatorInterface $validator): Response
    {
        if (!$this->isAuth())
            return $this->redirectToRoute('connexion');
        $doct = $this->getDoctrine()->getManager();
        $listing = $doct->getRepository(Listing::class)->find($id);
        $status = $request->request->get('status');
        $listing->setStatus($status);
        $feature = $request->request->get('feature');
        $listing->setFeature($feature == true);
        
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

    private function visit($id) {
        // visit number
        $doct = $this->getDoctrine()->getManager();
        $listing = $doct->getRepository(Listing::class)->find($id);
        $visit_number = $listing->getVisitNumber();
        $listing->setVisitNumber($visit_number + 1);
        $doct->flush();
    }

    /**
     * @Route("/admin/listing", name="admin_listing")
     */
    public function admin_index(): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $listings = $this->getDoctrine()->getRepository(Listing::class)->findAll();
        foreach ($listings as $listing) {
            $listing->city = $this->getDoctrine()->getRepository(City::class)->find($listing->getCityId());
            $listing->category = $this->getDoctrine()->getRepository(CategoryType::class)->find($listing->getCategoryId());
        }
        $cities = $this->getDoctrine()->getRepository(City::class)->findAll();
        $categories = $this->getDoctrine()->getRepository(CategoryType::class)->findAll();
        return $this->render('pages/admin/listing/index.html.twig', [
            'listings' => $listings,
            'cities' => $cities,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/admin/listing/edit/{id}", name="admin_listing_edit")
     */
    public function admin_edit($id): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
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
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $doct = $this->getDoctrine()->getManager();
        $listing = $doct->getRepository(Listing::class)->find($id);
        $doct->remove($listing);
        $doct->flush();
        return $this->redirectToRoute('admin_listing', [
            'id' => $listing->getId()
        ]);
    }

    /**
     * @Route("/admin/listing/search", name="admin_listing_search")
     */
    public function admin_search(Request $request): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $category_id = $request->request->get('category_id');
        $city_id = $request->request->get('city_id');
        $id = $request->request->get('id');
        $name = $request->request->get('name');
        $filter = [];
        if ($category_id != '0')
            $filter['category_id'] = $category_id;
        if ($city_id != '0')
            $filter['city_id'] = $city_id;
        if ($id != '')
            $filter['id'] = $id;
        if ($name != '')
            $filter['name'] = $name;
        
        $doct = $this->getDoctrine()->getManager();
        $listings = $doct->getRepository(Listing::class)->findWithFilter($filter);
        foreach ($listings as $listing) {
            $listing->city = $this->getDoctrine()->getRepository(City::class)->find($listing->getCityId());
            $listing->category = $this->getDoctrine()->getRepository(CategoryType::class)->find($listing->getCategoryId());
        }
        $cities = $this->getDoctrine()->getRepository(City::class)->findAll();
        $categories = $this->getDoctrine()->getRepository(CategoryType::class)->findAll();
        return $this->render('pages/admin/listing/index.html.twig', [
            'page' => 'listing',
            'subtitle' => 'My Listings',
            'listings' => $listings,
            'filter' => $filter,
            'cities' => $cities,
            'categories' => $categories
        ]);
    }
}