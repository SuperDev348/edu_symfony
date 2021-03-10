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
}
