<?php
/**
 * Timestample.
 */

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * TimestampleTrait.
 */
trait TimestampableTrait
{
    /**
     * \DateTime.
     *
     * @Gedmo\Timestampable(on="create")
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    protected $createdAt;

    /**
     * @var Datatime
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    protected $updatedAt;

    /**
     * Construct.
     */
    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * @return void
     */
    #[ORM\Preupdate]
    public function setUpdatedAtValue()
    {
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * Set the value of createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return mixed
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set the value of updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return mixed
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get the value of updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
