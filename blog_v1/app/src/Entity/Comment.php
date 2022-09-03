<?php
/**
 * Comment entity
 */

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Comment class
 */
#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'text')]
    private $comment_text;

    #[ORM\Column(type: 'string', length: 255)]
    private $autor;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private Post $post;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getCommentText(): ?string
    {
        return $this->comment_text;
    }

    /**
     * @param string $comment_text
     *
     * @return $this
     */
    public function setCommentText(string $comment_text): self
    {
        $this->comment_text = $comment_text;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAutor(): ?string
    {
        return $this->autor;
    }

    /**
     * @param string $autor
     *
     * @return $this
     */
    public function setAutor(string $autor): self
    {
        $this->autor = $autor;

        return $this;
    }

    /**
     * @return Post|null
     */
    public function getPost(): ?Post
    {
        return $this->post;
    }

    /**
     * @param Post|null $post
     *
     * @return $this
     */
    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }
}
