<?php

namespace App\Entity;

use App\Repository\ListingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ListingRepository::class)
 */
class Listing
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $user_id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $price_range;

    /**
     * @ORM\Column(type="text", length=500)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="boolean")
     */
    private $feature;

    /**
     * @ORM\Column(type="integer")
     */
    private $visit_number;

    /**
     * @ORM\Column(type="integer")
     */
    private $category_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $city_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $place_type_id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $wifi;

    /**
     * @ORM\Column(type="boolean")
     */
    private $parking;

    /**
     * @ORM\Column(type="boolean")
     */
    private $accept_card;

    /**
     * @ORM\Column(type="boolean")
     */
    private $garden;

    /**
     * @ORM\Column(type="boolean")
     */
    private $airport_taxi;

    /**
     * @ORM\Column(type="boolean")
     */
    private $terrace;

    /**
     * @ORM\Column(type="boolean")
     */
    private $toilet;

    /**
     * @ORM\Column(type="boolean")
     */
    private $air_conditioner;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $address;

    /**
     * @ORM\Column(type="decimal", precision=16, scale=13)
     */
    private $La_g;

    /**
     * @ORM\Column(type="decimal", precision=16, scale=13)
     */
    private $La_i;

    /**
     * @ORM\Column(type="decimal", precision=16, scale=13)
     */
    private $Ra_g;

    /**
     * @ORM\Column(type="decimal", precision=16, scale=13)
     */
    private $Ra_i;

    
    /**
     * @ORM\Column(type="decimal", precision=16, scale=13)
     */
    private $lat;

    /**
     * @ORM\Column(type="decimal", precision=16, scale=13)
     */
    private $lng;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $googlemap_address;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $website;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $facebook_url;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $instagram_url;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $youtube_url;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $twitter_url;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $google_url;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $pinterest_url;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $snapchat_url;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $facebook;

    /**
     * @ORM\Column(type="boolean")
     */
    private $instagram;

    /**
     * @ORM\Column(type="boolean")
     */
    private $youtube;

    /**
     * @ORM\Column(type="boolean")
     */
    private $twitter;

    /**
     * @ORM\Column(type="boolean")
     */
    private $google;

    /**
     * @ORM\Column(type="boolean")
     */
    private $pinterest;

    /**
     * @ORM\Column(type="boolean")
     */
    private $snapchat;

    /**
     * @ORM\Column(type="boolean")
     */
    private $monday;
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $tuesday;
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $wednesday;
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $thursday;
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $friday;
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $saturday;
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $sunday;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $monday_start_time;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $monday_end_time;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tuesday_start_time;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tuesday_end_time;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $wednesday_start_time;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $wednesday_end_time;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $thursday_start_time;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $thursday_end_time;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $friday_start_time;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $friday_end_time;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $saturday_start_time;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $saturday_end_time;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $sunday_start_time;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $sunday_end_time;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $cover_image;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $gallery_image;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $video;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getPriceRange(): ?string
    {
        return $this->price_range;
    }

    public function setPriceRange(string $price_range): self
    {
        $this->price_range = $price_range;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getFeature(): ?bool
    {
        return $this->feature;
    }

    public function setFeature(bool $feature): self
    {
        $this->feature = $feature;

        return $this;
    }

    public function getCityId(): ?string
    {
        return $this->city_id;
    }

    public function setCityId(string $city_id): self
    {
        $this->city_id = $city_id;

        return $this;
    }

    public function getVisitNumber(): ?string
    {
        return $this->visit_number;
    }

    public function setVisitNumber(string $visit_number): self
    {
        $this->visit_number = $visit_number;

        return $this;
    }

    public function getCategoryId(): ?string
    {
        return $this->category_id;
    }

    public function setCategoryId(string $category_id): self
    {
        $this->category_id = $category_id;

        return $this;
    }

    public function getPlaceTypeId(): ?int
    {
        return $this->place_type_id;
    }

    public function setPlaceTypeId(int $place_type_id): self
    {
        $this->place_type_id = $place_type_id;

        return $this;
    }

    public function getWifi(): ?bool
    {
        return $this->wifi;
    }

    public function setWifi(bool $wifi): self
    {
        $this->wifi = $wifi;

        return $this;
    }

    public function getParking(): ?bool
    {
        return $this->parking;
    }

    public function setParking(bool $parking): self
    {
        $this->parking = $parking;

        return $this;
    }

    public function getAcceptCard(): ?bool
    {
        return $this->accept_card;
    }

    public function setAcceptCard(bool $accept_card): self
    {
        $this->accept_card = $accept_card;

        return $this;
    }

    public function getGarden(): ?bool
    {
        return $this->garden;
    }

    public function setGarden(bool $garden): self
    {
        $this->garden = $garden;

        return $this;
    }

    public function getAirportTaxi(): ?bool
    {
        return $this->airport_taxi;
    }

    public function setAirportTaxi(bool $airport_taxi): self
    {
        $this->airport_taxi = $airport_taxi;

        return $this;
    }

    public function getTerrace(): ?bool
    {
        return $this->terrace;
    }

    public function setTerrace(bool $terrace): self
    {
        $this->terrace = $terrace;

        return $this;
    }

    public function getToilet(): ?bool
    {
        return $this->toilet;
    }

    public function setToilet(bool $toilet): self
    {
        $this->toilet = $toilet;

        return $this;
    }

    public function getAirConditioner(): ?bool
    {
        return $this->air_conditioner;
    }

    public function setAirConditioner(bool $air_conditioner): self
    {
        $this->air_conditioner = $air_conditioner;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getLaG(): ?float
    {
        return $this->La_g;
    }

    public function setLaG(?float $La_g): self
    {
        $this->La_g = $La_g;

        return $this;
    }

    public function getLaI(): ?float
    {
        return $this->La_i;
    }

    public function setLaI(?float $La_i): self
    {
        $this->La_i = $La_i;

        return $this;
    }

    public function getRaG(): ?float
    {
        return $this->Ra_g;
    }

    public function setRaG(?float $Ra_g): self
    {
        $this->Ra_g = $Ra_g;

        return $this;
    }

    public function getRaI(): ?float
    {
        return $this->Ra_i;
    }

    public function setRaI(?float $Ra_i): self
    {
        $this->Ra_i = $Ra_i;

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(?float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function setLng(?float $lng): self
    {
        $this->lng = $lng;

        return $this;
    }

    public function getGooglemapAddress(): ?string
    {
        return $this->googlemap_address;
    }

    public function setGooglemapAddress(?string $googlemap_address): self
    {
        $this->googlemap_address = $googlemap_address;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function getFacebook(): ?bool
    {
        return $this->facebook;
    }

    public function setFacebook(?bool $facebook): self
    {
        $this->facebook = $facebook;

        return $this;
    }

    public function getInstagram(): ?bool
    {
        return $this->instagram;
    }

    public function setInstagram(?bool $instagram): self
    {
        $this->instagram = $instagram;

        return $this;
    }

    public function getYoutube(): ?bool
    {
        return $this->youtube;
    }

    public function setYoutube(?bool $youtube): self
    {
        $this->youtube = $youtube;

        return $this;
    }

    public function getTwitter(): ?bool
    {
        return $this->twitter;
    }

    public function setTwitter(?bool $twitter): self
    {
        $this->twitter = $twitter;

        return $this;
    }

    public function getGoogle(): ?bool
    {
        return $this->google;
    }

    public function setGoogle(?bool $google): self
    {
        $this->google = $google;

        return $this;
    }

    public function getPinterest(): ?bool
    {
        return $this->pinterest;
    }

    public function setPinterest(?bool $pinterest): self
    {
        $this->pinterest = $pinterest;

        return $this;
    }

    public function getSnapchat(): ?bool
    {
        return $this->snapchat;
    }

    public function setSnapchat(?bool $snapchat): self
    {
        $this->snapchat = $snapchat;

        return $this;
    }

    public function getFacebookUrl(): ?string
    {
        return $this->facebook_url;
    }

    public function setFacebookUrl(?string $facebook_url): self
    {
        $this->facebook_url = $facebook_url;

        return $this;
    }

    public function getInstagramUrl(): ?string
    {
        return $this->instagram_url;
    }

    public function setInstagramUrl(?string $instagram_url): self
    {
        $this->instagram_url = $instagram_url;

        return $this;
    }

    public function getYoutubeUrl(): ?string
    {
        return $this->youtube_url;
    }

    public function setYoutubeUrl(?string $youtube_url): self
    {
        $this->youtube_url = $youtube_url;

        return $this;
    }

    public function getTwitterUrl(): ?string
    {
        return $this->twitter_url;
    }

    public function setTwitterUrl(?string $twitter_url): self
    {
        $this->twitter_url = $twitter_url;

        return $this;
    }

    public function getGoogleUrl(): ?string
    {
        return $this->google_url;
    }

    public function setGoogleUrl(?string $google_url): self
    {
        $this->google_url = $google_url;

        return $this;
    }

    public function getPinterestUrl(): ?string
    {
        return $this->pinterest_url;
    }

    public function setPinterestUrl(?string $pinterest_url): self
    {
        $this->pinterest_url = $pinterest_url;

        return $this;
    }

    public function getSnapchatUrl(): ?string
    {
        return $this->snapchat_url;
    }

    public function setSnapchatUrl(?string $snapchat_url): self
    {
        $this->snapchat_url = $snapchat_url;

        return $this;
    }
    
    public function getMonday(): ?bool
    {
        return $this->monday;
    }

    public function setMonday(?bool $monday): self
    {
        $this->monday = $monday;

        return $this;
    }

    public function getTuesday(): ?bool
    {
        return $this->tuesday;
    }

    public function setTuesday(?bool $tuesday): self
    {
        $this->tuesday = $tuesday;

        return $this;
    }
    
    public function getWednesday(): ?bool
    {
        return $this->wednesday;
    }

    public function setWednesday(?bool $wednesday): self
    {
        $this->wednesday = $wednesday;

        return $this;
    }
    
    public function getThursday(): ?bool
    {
        return $this->thursday;
    }

    public function setThursday(?bool $thursday): self
    {
        $this->thursday = $thursday;

        return $this;
    }
    
    public function getFriday(): ?bool
    {
        return $this->friday;
    }

    public function setFriday(?bool $friday): self
    {
        $this->friday = $friday;

        return $this;
    }
    
    public function getSaturday(): ?bool
    {
        return $this->saturday;
    }

    public function setSaturday(?bool $saturday): self
    {
        $this->saturday = $saturday;

        return $this;
    }

    public function getSunday(): ?bool
    {
        return $this->sunday;
    }

    public function setSunday(?bool $sunday): self
    {
        $this->sunday = $sunday;

        return $this;
    }

    public function getMondayStartTime(): ?int
    {
        return $this->monday_start_time;
    }

    public function setMondayStartTime(?int $monday_start_time): self
    {
        $this->monday_start_time = $monday_start_time;

        return $this;
    }

    public function getMondayEndTime(): ?int
    {
        return $this->monday_end_time;
    }

    public function setMondayEndTime(?int $monday_end_time): self
    {
        $this->monday_end_time = $monday_end_time;

        return $this;
    }

    public function getTuesdayStartTime(): ?int
    {
        return $this->tuesday_start_time;
    }

    public function setTuesdayStartTime(?int $tuesday_start_time): self
    {
        $this->tuesday_start_time = $tuesday_start_time;

        return $this;
    }

    public function getTuesdayEndTime(): ?int
    {
        return $this->tuesday_end_time;
    }

    public function setTuesdayEndTime(?int $tuesday_end_time): self
    {
        $this->tuesday_end_time = $tuesday_end_time;

        return $this;
    }

    public function getWednesdayStartTime(): ?int
    {
        return $this->wednesday_start_time;
    }

    public function setWednesdayStartTime(?int $wednesday_start_time): self
    {
        $this->wednesday_start_time = $wednesday_start_time;

        return $this;
    }

    public function getWednesdayEndTime(): ?int
    {
        return $this->wednesday_end_time;
    }

    public function setWednesdayEndTime(?int $wednesday_end_time): self
    {
        $this->wednesday_end_time = $wednesday_end_time;

        return $this;
    }

    public function getThursdayStartTime(): ?int
    {
        return $this->thursday_start_time;
    }

    public function setThursdayStartTime(?int $thursday_start_time): self
    {
        $this->thursday_start_time = $thursday_start_time;

        return $this;
    }

    public function getThursdayEndTime(): ?int
    {
        return $this->thursday_end_time;
    }

    public function setThursdayEndTime(?int $thursday_end_time): self
    {
        $this->thursday_end_time = $thursday_end_time;

        return $this;
    }

    public function getFridayStartTime(): ?int
    {
        return $this->friday_start_time;
    }

    public function setFridayStartTime(?int $friday_start_time): self
    {
        $this->friday_start_time = $friday_start_time;

        return $this;
    }

    public function getFridayEndTime(): ?int
    {
        return $this->friday_end_time;
    }

    public function setFridayEndTime(?int $friday_end_time): self
    {
        $this->friday_end_time = $friday_end_time;

        return $this;
    }

    public function getSaturdayStartTime(): ?int
    {
        return $this->saturday_start_time;
    }

    public function setSaturdayStartTime(?int $saturday_start_time): self
    {
        $this->saturday_start_time = $saturday_start_time;

        return $this;
    }

    public function getSaturdayEndTime(): ?int
    {
        return $this->saturday_end_time;
    }

    public function setSaturdayEndTime(?int $saturday_end_time): self
    {
        $this->saturday_end_time = $saturday_end_time;

        return $this;
    }

    public function getSundayStartTime(): ?int
    {
        return $this->sunday_start_time;
    }

    public function setSundayStartTime(?int $sunday_start_time): self
    {
        $this->sunday_start_time = $sunday_start_time;

        return $this;
    }

    public function getSundayEndTime(): ?int
    {
        return $this->sunday_end_time;
    }

    public function setSundayEndTime(?int $sunday_end_time): self
    {
        $this->sunday_end_time = $sunday_end_time;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->cover_image;
    }

    public function setCoverImage(?string $cover_image): self
    {
        $this->cover_image = $cover_image;

        return $this;
    }

    public function getGalleryImage(): ?string
    {
        return $this->gallery_image;
    }

    public function setGalleryImage(?string $gallery_image): self
    {
        $this->gallery_image = $gallery_image;

        return $this;
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(?string $video): self
    {
        $this->video = $video;

        return $this;
    }
}
