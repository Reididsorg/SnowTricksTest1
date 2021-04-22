<?php


namespace App\Entity;

use App\Validator as AcmeAssert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Table()
 * @ORM\Entity()
 *
 * @UniqueEntity(
 *     fields={"name"},
 *     message="Une image avec le même nom existe déjà ! Merci de choisir un autre nom"
 * )
 *
 */
class Image extends AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(type="string",  length=255)
     *
     * @AcmeAssert\ContainsToto()
     * @Assert\NotBlank(message="Champ 'Nom' obligatoire !")
     *
     */
    protected string $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string",  length=255)
     *
     * @Assert\NotBlank(message="Champ 'Description' obligatoire !")
     *
     */
    protected string $alt;

    /**
     * @var string
     *
     * @ORM\Column(type="string",  length=255)
     *
     * @Assert\NotNull(message="Champ 'Sélectionnez un fichier' obligatoire !")
     * @Assert\Image(
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif"},
     *     mimeTypesMessage="Le fichier doit être de type jpg, jpeg, png ou gif",
     *     minPixels=1,
     *     minPixelsMessage="Image obligatoire !",
     *     allowPortrait=false,
     *     allowPortraitMessage="Seules les images en paysage sont autorisées",
     *     allowSquare=false,
     *     allowSquareMessage="Seules les images en paysage sont autorisées",
     *     minWidth=500,
     *     minWidthMessage="La largeur minimum est de 500px",
     *     maxWidth=2000,
     *     maxWidthMessage="La largeur maximum est de 2000px",
     *     minHeight=300,
     *     minHeightMessage="La hauteur minimum est de 300px",
     *     maxHeight=1000,
     *     maxHeightMessage="La hauteur maximum est de 1000px"
     * )
     * @Assert\File(
     *     maxSize="1M",
     *     maxSizeMessage="Le fichier est trop grand ({{ size }} {{ suffix }}). La taille maximum est de : {{ limit }} {{ suffix }}."
     * )
     *
     */
    protected string $path;

    /**
     * @var Trick
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Trick", inversedBy="images")
     * @ORM\JoinColumn(name="trick_id", referencedColumnName="id")
     */
    protected Trick $trick;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected bool $main = false;

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
    public function getAlt(): string
    {
        return $this->alt;
    }

    /**
     * @param string $alt
     */
    public function setAlt(string $alt): void
    {
        $this->alt = $alt;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
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

    /**
     * @return bool
     */
    public function isMain(): bool
    {
        return $this->main;
    }

    /**
     * @param bool $main
     */
    public function setMain(bool $main): void
    {
        $this->main = $main;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        /*if(!isset($name) || !isset($alt) || !isset($path)){
            $context->buildViolation('Fichier non sélectionné. Merci de sélectionner un fichier')
                ->atPath('path')
                ->addViolation();
        }*/
        //dump($this);
        //dump($this->getName());
        //dump($this->getAlt());
        //dump($this->getPath());
        //exit;



        /*if($this->getMainImage() === null) {
            $context->buildViolation('Image principale obligatoire !!!!!')
                ->atPath('mainImage')
                ->addViolation();
        }*/
    }
}