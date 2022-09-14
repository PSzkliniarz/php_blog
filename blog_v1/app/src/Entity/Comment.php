<?php
/**
 * Comment entity.
 */

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Comment class.
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

    /**
     * Comment Author.
     */
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Email]
    private ?string $autor;

    /**
     * Comment Post.
     */
    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'comments')] #[ORM\JoinColumn(nullable: false)]
    private Post $post;

    /**
     * @return int|null return
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null return
     */
    public function getCommentText(): ?string
    {
        return $this->commentText;
    }

    /**
     * @param string $commentText param
     *
     * @return $this return
     */
    public function setCommentText(string $commentText): self
    {
        $this->commentText = $commentText;

        return $this;
    }

    /**
     * @return string|null return
     */
    public function getAutor(): ?string
    {
        return $this->autor;
    }

    /**
     * @param string $autor param
     *
     * @return $this return
     */
    public function setAutor(string $autor): self
    {
        $this->autor = $autor;

        return $this;
    }

    /**
     * @return Post|null return
     */
    public function getPost(): ?Post
    {
        return $this->post;
    }

    /**
     * @param Post|null $post param
     *
     * @return $this return
     */
    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }
}
