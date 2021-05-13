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
     * @var string
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

    public function __construct()
    {
        $this->roles[] = 'ROLE_USER';
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
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
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
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
}