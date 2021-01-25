<?php

namespace App\Entity;

use App\Repository\TrackingRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="eurocave_tracking")
 * @ORM\Entity(repositoryClass=TrackingRepository::class)
 */
class Tracking
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $login;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="trackings")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $user;
    /**
     * @var DateTime
     *
     * @ORM\Column(name="lastLogin", type="datetime")
     */
    private $lastLogin;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getUserId()
    {
        return $this->getUser()->getId();
    }
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTimeInterface $lastLogin): self
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }
}
