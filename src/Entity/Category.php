<?php


namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class Category extends AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, unique=true)
     *
     */
    protected string $name;

//    /**
//     * @ORM\OneToMany(targetEntity="App\Entity\Trick", mappedBy="category", orphanRemoval=true)
//     *
//     */
//    protected Collection $trick;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /*
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

//    /*
//     * @return Trick
//     */
//    public function getTrick()
//    {
//        return $this->trick;
//    }
//
//    /*
//     * @param Trick $trick
//     */
//    public function setTrick(Trick $trick): void
//    {
//        $this->tricks = $trick;
//    }
}