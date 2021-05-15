<?php


namespace App\Entity;

use App\Service\FileUploader;
use App\Validator as AcmeAssert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

//*     allowPortrait=false,
//*     allowPortraitMessage="Les images en portrait ne sont pas acceptées.",

    /**
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     *
     * @Assert\Image(
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif"},
     *     mimeTypesMessage="Le fichier doit être de type jpg, jpeg, png ou gif",
     *     minPixels=1,
     *     minPixelsMessage="Image obligatoire !",
     *     minWidth=1500,
     *     minWidthMessage="La largeur minimum est de 1500px",
     *     maxWidth=6300,
     *     maxWidthMessage="La largeur maximum est de 6300px",
     *     minHeight=840,
     *     minHeightMessage="La hauteur minimum est de 840px",
     *     maxHeight=3500,
     *     maxHeightMessage="La hauteur maximum est de 3500px"
     * )
     * @Assert\File(
     *     maxSize="6M",
     *     maxSizeMessage="Le fichier est trop grand ({{ size }} {{ suffix }}). La taille maximum est de : {{ limit }} {{ suffix }}."
     * )
     *
     */
    protected ?string $fileName = null;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     *
     */
    protected ?string $path = null;

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


    // $name
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        if ($name === null) {
            $name = '';
        }
        $this->name = $name;
    }

    // $alt
    public function getAlt(): string
    {
        return $this->alt;
    }

    public function setAlt(?string $alt): void
    {
        if ($alt === null) {
            $alt = '';
        }
        $this->alt = $alt;
    }

    // fileName
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): void
    {
        if ($fileName === null) {
            $fileName = '';
        }
        $this->fileName = $fileName;
    }


    // path
    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): void
    {
        $this->path = $path;
    }

    // $trick
    public function getTrick(): Trick
    {
        return $this->trick;
    }

    public function setTrick(Trick $trick): void
    {
        $this->trick = $trick;
    }

    // $main
    public function isMain(): bool
    {
        return $this->main;
    }

    public function setMain(bool $main): void
    {
        $this->main = $main;
    }

}