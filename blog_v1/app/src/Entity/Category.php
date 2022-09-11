<?php
/**
 * Category entity.
 */

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Category class.
 */
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    use TimestampableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 64)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 64)]
    private $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->name;
    }
}
