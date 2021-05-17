<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

class AbstractEntity
{
    use TimestampableTrait;

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected ?int $id = null;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = null;
    }
}