<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GroupRepository::class)
 * @ORM\Table(name="`eurocave_group`")
 */
class Group
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;
    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="group")
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    public function __toString(): string
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
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setGroup($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getGroup() === $this) {
                $user->setGroup(null);
            }
        }

        return $this;
    }
}
