<?php

namespace App\Entity;

use App\Repository\ArchivedCustomersRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArchivedCustomersRepository::class)]
class ArchivedCustomers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $reason_archived = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_archived = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getReasonArchived(): ?string
    {
        return $this->reason_archived;
    }

    public function setReasonArchived(string $reason_archived): static
    {
        $this->reason_archived = $reason_archived;

        return $this;
    }

    public function getDateArchived(): ?\DateTimeImmutable
    {
        return $this->date_archived;
    }

    public function setDateArchived(\DateTimeImmutable $date_archived): static
    {
        $this->date_archived = $date_archived;

        return $this;
    }
}
