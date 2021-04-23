<?php


namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Table()
 * @ORM\Entity()
 *
 * @UniqueEntity(
 *     fields={"name"},
 *     message="Ce trick existe déjà !"
 * )
 *
 */
class Trick extends AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, unique=true)
     *
     * @Assert\NotBlank(message="Champ 'Nom' obligatoire")
     * @Assert\Length(max=100, maxMessage="Le nom ne doit pas faire plus de 100 caractères")
     *
     */
    protected string $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank(message="Champ 'Description' obligatoire")
     * @Assert\Length(min=20, minMessage="La description doit faire au moins 20 caractères")
     * @Assert\Length(max=1000, maxMessage="La description ne doit pas faire plus de 1000 caractères")
     *
     */
    protected string $description;

    /**
     * @var string
     *
     * @ORM\Column(type="string",  length=255, unique=true)
     * @Gedmo\Slug(fields={"name"})
     */
    protected string $slug;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Image", mappedBy="trick", cascade={"persist", "remove"})
     *
     * @Assert\Valid()
     *
     */
    protected Image $mainImage;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="trick", orphanRemoval=true)
     */
    protected $comments;

    /**
     * @var Image[]|Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="trick", orphanRemoval=true, cascade={"persist", "remove"})
     *
     * @Assert\Valid()
     *
     */
    protected Collection $images;

    /**
     * @var Video[]|Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Video", mappedBy="trick", orphanRemoval=true, cascade={"persist", "remove"})
     *
     * @Assert\Valid()
     *
     */
    protected Collection $videos;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="tricks")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     *
     * @Assert\NotNull(message="Champ 'Catégorie' obligatoire")
     *
     */
    protected $category;

    public function __construct()
    {
        parent::__construct();
        $this->images = new ArrayCollection();
        $this->videos = new ArrayCollection();
    }

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
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return Comment
     */
    public function getComments(): Comment
    {
        return $this->comments;
    }

    /**
     * @param mixed $comments
     */
    public function setComments($comments): void
    {
        $this->comments = $comments;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): ?Collection
    {
        return $this->images;
    }

    public function addImage(Image $image)
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setTrick($this);
        }
        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getTrick() === $this) {
                $image->setTrick(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Video[]
     */
    public function getVideos(): ?Collection
    {
        return $this->videos;
    }

    public function addVideo(Video $video)
    {
        if (!$this->videos->contains($video)) {
            $this->videos[] = $video;
            $video->setTrick($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): self
    {
        if ($this->videos->contains($video)) {
            $this->videos->removeElement($video);
            // set the owning side to null (unless already changed)
            if ($video->getTrick() === $this) {
                $video->setTrick(null);
            }
        }

        return $this;
    }



    public function getMainImage(): ?Image
    {
        return $this->mainImage;
    }


    public function setMainImage($mainImage): void
    {
        $this->mainImage = $mainImage;
    }


    /**
     * @return mixed
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category): void
    {
        $this->category = $category;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        /*if(!isset($mainImage)) {
            $context->buildViolation('Image principale obligatoire !!!!!')
                ->atPath('images')
                ->addViolation();
        }*/
    }
}