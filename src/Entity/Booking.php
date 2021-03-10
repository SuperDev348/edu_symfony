<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 */
class Booking
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
    private $status;

    /**
     * @ORM\Column(type="integer")
     */
    private $adult_number;

    /**
     * @ORM\Column(type="string")
     */
    private $time;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     */
    private $listing_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $children_number;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $phone_number;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAdultNumber(): ?int
    {
        return $this->adult_number;
    }

    public function setAdultNumber(int $adult_number): self
    {
        $this->adult_number = $adult_number;

        return $this;
    }

    public function getTime(): ?string
    {
        return $this->time;
    }

    public function setTime(string $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getListingId(): ?int
    {
        return $this->listing_id;
    }

    public function setListingId(int $listing_id): self
    {
        $this->listing_id = $listing_id;

        return $this;
    }

    public function getChildrenNumber(): ?int
    {
        return $this->children_number;
    }

    public function setChildrenNumber(int $children_number): self
    {
        $this->children_number = $children_number;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(string $phone_number): self
    {
        $this->phone_number = $phone_number;

        return $this;
    }
}
