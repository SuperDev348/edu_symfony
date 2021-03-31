<?php

namespace App\Entity;

use App\Repository\WishlistRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WishlistRepository::class)
 */
class Wishlist
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $listing_id;

    public function getId(): ?int
    {
        return $this->id;
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
}
