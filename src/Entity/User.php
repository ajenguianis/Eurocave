<?php

namespace App\Entity;

use App\Entity\Group;
use App\Entity\Tracking;
use App\Repository\UserRepository;
use App\Services\EncryptService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="eurocave_users")
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;
    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive=true;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Group", inversedBy="users")
     */
    private $group;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Tracking", mappedBy="User")
     */
    private $trackings;
    /**
     * @ORM\Column(name="serialNumber", type="string", length=255, unique=true, nullable=true)
     */
    private $serialNumber;

    /**
     * @return mixed
     */
    public function getSerialNumber()
    {
        return $this->serialNumber ? EncryptService::decodeData($this->serialNumber) : null;
    }
    /**
     * @param mixed $serialNumber
     */
    public function setSerialNumber($serialNumber): void
    {
        $serialNumber=$serialNumber ? EncryptService::encodeData($serialNumber) : null;
        $this->serialNumber = $serialNumber;
    }
    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->trackings = new ArrayCollection();
    }
    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }
    

    /**
     * @return Collection|Tracking[]
     */
    public function getTrackings(): Collection
    {
        return $this->trackings;
    }

    public function addTracking(Tracking $tracking): self
    {
        if (!$this->trackings->contains($tracking)) {
            $this->trackings[] = $tracking;
            $tracking->setUser($this);
        }

        return $this;
    }

    public function removeTracking(Tracking $tracking): self
    {
        if ($this->trackings->contains($tracking)) {
            $this->trackings->removeElement($tracking);
            // set the owning side to null (unless already changed)
            if ($tracking->getUser() === $this) {
                $tracking->setUser(null);
            }
        }

        return $this;
    }

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(?Group $group): self
    {
        $this->group = $group;

        return $this;
    }
}
