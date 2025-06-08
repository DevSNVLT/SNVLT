<?php

namespace App\Entity\Blog;

use App\Entity\User;
use App\Entity\Ventes\Client;
use App\Repository\Blog\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'blog.comment')]
#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ArticleBlog $article = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Abonne $utilisateur = null;

    #[ORM\Column(nullable: true)]
    private ?int $reply_id = null;

    public function __construct(ArticleBlog $article)
    {
        $this->article = $article;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getArticle(): ?ArticleBlog
    {
        return $this->article;
    }

    public function setArticle(?ArticleBlog $article): self
    {
        $this->article = $article;

        return $this;
    }

    public function getUtilisateur(): ?Abonne
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Abonne $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getReplyId(): ?int
    {
        return $this->reply_id;
    }

    public function setReplyId(?int $reply_id): self
    {
        $this->reply_id = $reply_id;

        return $this;
    }
}
