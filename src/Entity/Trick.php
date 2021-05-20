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
     * @Assert\Length(min=4, minMessage="Le nom doit faire au moins 4 caractères")
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
     *
     * @Gedmo\Slug(fields={"name"})
     */
    protected string $slug;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="tricks")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="trick", fetch="EXTRA_LAZY", orphanRemoval=true)
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    protected Collection $comments;

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
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     *
     * @Assert\NotNull(message="Champ 'Catégorie' obligatoire")
     *
     */
    protected $category;

    // Constructor
    public function __construct()
    {
        parent::__construct();
        $this->images = new ArrayCollection();
        $this->videos = new ArrayCollection();
    }

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

    // $description
    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        if ($description === null) {
            $description = '';
        }
        $this->description = $description;
    }

    // $slug
    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    // $tricks
    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    // $comments
    public function getComments(): ?Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment)
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setTrick($this);
        }
        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getTrick() === $this) {
                $comment->setTrick(null);
            }
        }
        return $this;
    }

    // $images
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

    // $videos
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

    // $category
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory($category): void
    {
        $this->category = $category;
    }

}