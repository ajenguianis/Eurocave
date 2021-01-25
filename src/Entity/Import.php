<?php

namespace App\Entity;

use App\Repository\ImportRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="`eurocave_import`")
 * @ORM\Entity(repositoryClass=ImportRepository::class)
 */
class Import
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="fileName", type="string", length=255)
     */
    private $fileName;
    /**
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;
    /**
     * @ORM\Column(name="importedAt", type="datetime")
     */
    private $importedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getImportedAt(): ?\DateTimeInterface
    {
        return $this->importedAt;
    }

    public function setImportedAt(\DateTimeInterface $importedAt): self
    {
        $this->importedAt = $importedAt;

        return $this;
    }


}
