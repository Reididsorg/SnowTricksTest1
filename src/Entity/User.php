<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 *
 * @UniqueEntity(
 *     fields={"username"},
 *     message="Ce nom d'utilisateur est déjà enregistré !"
 * )
 * @UniqueEntity(
 *     fields={"email"},
 *     message="Cet email est déjà enregistré !"
 * )
 *
 */
class User extends AbstractEntity implements UserInterface
{
    /**
     * @var ?string
     *
     * @ORM\Column(type="string",  length=100, unique=true)
     *
     * @Assert\NotBlank(message="Champ obligatoire !")
     * @Assert\Length(min=4, minMessage="Le nom d'utilisateur est trop court. Il doit faire au moins 4 caractères")
     * @Assert\Length(max=100, maxMessage="Le nom d'utilisateur est trop long. Il doit pas faire plus de 100 caractères")
     *
     */
    protected string $username;

    /**
     * @var string
     *
     * @ORM\Column(type="string",  length=255)
     *
     * @Assert\NotBlank(message="Champ obligatoire !")
     * @Assert\Length(min=4, minMessage="Le mot de passe est trop court. Il doit faire au moins 4 caractères")
     * @Assert\Length(max=100, maxMessage="Le mot de passe est trop long. Il ne doit pas faire plus de 255 caractères")
     *
     */
    protected string $password;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected array $roles;

    /**
     * @var string
     *
     * @ORM\Column(type="string",  length=100, unique=true)
     *
     * @Assert\NotBlank(message="Champ obligatoire !")
     * @Assert\Length(max=100, maxMessage="L'email ne doit pas faire plus de 100 caractères")
     * @Assert\Email(message="Ceci n'est pas une adresse email valide")
     *
     */
    protected string $email;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="user", orphanRemoval=true)
     */
    protected $comments;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $passwordRequestedAt;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $token;

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
     *     allowPortrait=false,
     *     allowPortraitMessage="Les photos en portrait ne sont pas acceptées.",
     *     allowLandscape=false,
     *     allowLandscapeMessage="Les photos en paysage ne sont pas acceptées.",
     *     minWidth=200,
     *     minWidthMessage="La largeur minimum est de 200px",
     *     maxWidth=1000,
     *     maxWidthMessage="La largeur maximum est de 1000px",
     *     minHeight=200,
     *     minHeightMessage="La hauteur minimum est de 200px",
     *     maxHeight=1000,
     *     maxHeightMessage="La hauteur maximum est de 1000px"
     * )
     * @Assert\File(
     *     maxSize="6M",
     *     maxSizeMessage="Le fichier est trop grand ({{ size }} {{ suffix }}). La taille maximum est de : {{ limit }} {{ suffix }}."
     * )
     * @Assert\NotBlank(message="Image obligatoire !", groups={"registration"})
     *
     */
    protected ?string $imageFileName = null;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    protected ?string $imageAlt = null;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    protected ?string $imagePath = null;

    public function __construct()
    {
        $this->roles[] = 'ROLE_USER';
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getUsername(): ?string
    {
        if ($this->username === null) {
            $this->username = '';
        }

        return $this->username;
    }

    /**
     * @param string|null $username
     */
    public function setUsername(?string $username): void
    {
        $this->username = is_null($username) ? : $username;
        //$this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        //$this->email = $email;
        $this->email = is_null($email) ? : $email;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    /**
     * @return mixed
     */
    public function getComments()
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

    public function getRoles()
    {
        return $this->roles;
    }

    public function getSalt()
    {
        return;
    }

    public function eraseCredentials()
    {
        return;
    }

    /**
     * @return \DateTime
     */
    public function getPasswordRequestedAt(): \DateTime
    {
        return $this->passwordRequestedAt;
    }

    /**
     * @param \DateTime $passwordRequestedAt
     */
    public function setPasswordRequestedAt(?\DateTime $passwordRequestedAt = null): void
    {
        $this->passwordRequestedAt = $passwordRequestedAt;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(?string $token = null): void
    {
        $this->token = $token;
    }

    /**
     * @return string|null
     */
    public function getImageFileName(): ?string
    {
        return $this->imageFileName;
    }

    /**
     * @param string|null $imageFileName
     */
    public function setImageFileName(?string $imageFileName): void
    {
        $this->imageFileName = $imageFileName;
    }

    /**
     * @return string
     */
    public function getImageAlt(): ?string
    {
        return $this->imageAlt;
    }

    /**
     * @param string $imageAlt
     */
    public function setImageAlt(?string $imageAlt): void
    {
        $this->imageAlt = $imageAlt;
    }

    /**
     * @return string
     */
    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    /**
     * @param string $imagePath
     */
    public function setImagePath(?string $imagePath): void
    {
        $this->imagePath = $imagePath;
    }
}