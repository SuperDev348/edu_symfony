<?php

namespace App\Entity;

use App\Repository\BusinessownerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BusinessownerRepository::class)
 */
class Businessowner
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="le Champ numregistre est obligatoire")
     * @ORM\Column(type="string", length=40)
     */
    private $numregistre;

    /**
     * @Assert\NotBlank(message="le Champ adresse est obligatoire")
     * @ORM\Column(type="string", length=40)
     */
    private $adresse;

    /**
     * @Assert\NotBlank(message="le Champ type est obligatoire")
     * @ORM\Column(type="string", length=20)
     */
    private $type;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="Businessowner", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $Useraccount;

    /**
     * @ORM\Column(type="boolean")
     */
    private $etat;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumregistre(): ?string
    {
        return $this->numregistre;
    }

    public function setNumregistre(string $numregistre): self
    {
        $this->numregistre = $numregistre;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getUseraccount(): ?User
    {
        return $this->Useraccount;
    }

    public function setUseraccount(User $Useraccount): self
    {
        $this->Useraccount = $Useraccount;

        return $this;
    }

    public function getEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(bool $etat): self
    {
        $this->etat = $etat;

        return $this;
    }
}
