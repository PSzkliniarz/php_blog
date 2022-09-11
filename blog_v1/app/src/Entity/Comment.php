<?php
/**
 * Comment entity
 */

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Comment class
 */
#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    /**
     * Primary key.
     *
     * @var int|null Id
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    /**
     * Comment text.
     *
     * @var string|null CommentText
     */

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 200)]
    private ?string $commentText;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $autor;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'comments')]    #[ORM\JoinColumn(nullable: false)]
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
        return $this->commentText;
    }

    /**
     * @param string $commentText
     *
     * @return $this
     */
    public function setCommentText(string $commentText): self
    {
        $this->commentText = $commentText;

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
