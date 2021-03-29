<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User 
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="le Champ nom est obligatoire")
     * @ORM\Column(type="string", length=20)
     */
    private $nom;

    /**
     * @Assert\NotBlank(message="le Champ prenom est obligatoire")
     * @ORM\Column(type="string", length=20)
     */
    private $prenom;

    /**
     * @Assert\NotBlank(message="le Champ mail est obligatoire")
     * @Assert\Email(message="mail non valid")
     * @ORM\Column(type="string", length=50)
     */
    private $mail;

    /**
     *
     * @Assert\NotBlank(message="le Champ prenom est obligatoire")
     * @Assert\Length(
     *     min=4,
     *     max=8,
     *      minMessage = "Your password must be at least {{ limit }} characters long",
     *      maxMessage = "Your password cannot be longer than {{ limit }} characters",
     * )
     * @ORM\Column(type="string", length=72)
     */
    private $password;

    /**
     * @ORM\OneToOne(targetEntity=Businessowner::class, mappedBy="Useraccount", cascade={"persist", "remove"})
     */
    private $Businessowner;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $type;

    /**
     * @ORM\Column(type="boolean")
     */
    private $ban;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $image;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getBusinessowner(): ?Businessowner
    {
        return $this->Businessowner;
    }

    public function setBusinessowner(Businessowner $Businessowner): self
    {
        // set the owning side of the relation if necessary
        if ($Businessowner->getUseraccount() !== $this) {
            $Businessowner->setUseraccount($this);
        }

        $this->businessowner = $Businessowner;

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

    public function getBan(): ?bool
    {
        return $this->ban;
    }

    public function setBan(bool $ban): self
    {
        $this->ban = $ban;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }
}
