<?php

namespace App\Entity;

use App\Repository\SettingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SettingRepository::class)
 */
class Setting
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $visit_number;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $booking_block;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $booking_block_from;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $booking_block_to;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVisitNumber(): ?int
    {
        return $this->visit_number;
    }

    public function setVisitNumber(?int $visit_number): self
    {
        $this->visit_number = $visit_number;

        return $this;
    }

    public function getBookingBlock(): ?bool
    {
        return $this->booking_block;
    }

    public function setBookingBlock(?bool $booking_block): self
    {
        $this->booking_block = $booking_block;

        return $this;
    }

    public function getBookingBlockFrom(): ?\DateTimeInterface
    {
        return $this->booking_block_from;
    }

    public function setBookingBlockFrom(\DateTimeInterface $booking_block_from): self
    {
        $this->booking_block_from = $booking_block_from;

        return $this;
    }

    public function getBookingBlockTo(): ?\DateTimeInterface
    {
        return $this->booking_block_to;
    }

    public function setBookingBlockTo(\DateTimeInterface $booking_block_to): self
    {
        $this->booking_block_to = $booking_block_to;

        return $this;
    }
}
