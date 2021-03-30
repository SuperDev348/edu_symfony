<?php

namespace App\Entity;

use App\Repository\BlogCommentLikeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BlogCommentLikeRepository::class)
 */
class BlogCommentLike
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
    private $user_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $blogcomment_id;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getBlogcommentId(): ?int
    {
        return $this->blogcomment_id;
    }

    public function setBlogcommentId(int $blogcomment_id): self
    {
        $this->blogcomment_id = $blogcomment_id;

        return $this;
    }
}
