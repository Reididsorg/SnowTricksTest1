<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class Video extends AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(type="string",  length=255)
     *
     * @Assert\NotBlank(message="Champ obligatoire")
     *
     */
    protected string $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string",  length=255)
     *
     * @Assert\NotBlank(message="Champ obligatoire")
     * @Assert\Url(message="Url non valide. Merci de saisir une url valide")
     *
     */
    protected string $url;

    /**
     * @var Trick
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Trick", inversedBy="videos")
     * @ORM\JoinColumn(name="trick_id", referencedColumnName="id")
     */
    protected Trick $trick;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return Trick
     */
    public function getTrick(): Trick
    {
        return $this->trick;
    }

    /**
     * @param Trick $trick
     */
    public function setTrick(Trick $trick): void
    {
        $this->trick = $trick;
    }
}