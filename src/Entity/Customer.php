<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $surname = null;

    #[ORM\Column(length: 15)]
    private ?string $phone = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @var Collection<int, Loan>
     */
    #[ORM\OneToMany(targetEntity: Loan::class, mappedBy: 'customer_id')]
    private Collection $loans;

    /**
     * @var Collection<int, HistoryCustomers>
     */
    #[ORM\OneToMany(targetEntity: HistoryCustomers::class, mappedBy: 'customer_id')]
    private Collection $historyCustomers;

    #[ORM\OneToOne(mappedBy: 'customer', cascade: ['persist', 'remove'])]
    private ?Address $address = null;

    public function __construct()
    {
        $this->loans = new ArrayCollection();
        $this->historyCustomers = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
    }

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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return Collection<int, Loan>
     */
    public function getLoans(): Collection
    {
        return $this->loans;
    }

    public function addLoan(Loan $loan): static
    {
        if (!$this->loans->contains($loan)) {
            $this->loans->add($loan);
            $loan->setCustomerId($this);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }


    public function removeLoan(Loan $loan): static
    {
        if ($this->loans->removeElement($loan)) {
            // set the owning side to null (unless already changed)
            if ($loan->getCustomerId() === $this) {
                $loan->setCustomerId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, HistoryCustomers>
     */
    public function getHistoryCustomers(): Collection
    {
        return $this->historyCustomers;
    }

    public function addHistoryCustomer(HistoryCustomers $historyCustomer): static
    {
        if (!$this->historyCustomers->contains($historyCustomer)) {
            $this->historyCustomers->add($historyCustomer);
            $historyCustomer->setCustomerId($this);
        }

        return $this;
    }

    public function removeHistoryCustomer(HistoryCustomers $historyCustomer): static
    {
        if ($this->historyCustomers->removeElement($historyCustomer)) {
            // set the owning side to null (unless already changed)
            if ($historyCustomer->getCustomerId() === $this) {
                $historyCustomer->setCustomerId(null);
            }
        }

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): static
    {
        // set the owning side of the relation if necessary
        if ($address->getCustomer() !== $this) {
            $address->setCustomer($this);
        }

        $this->address = $address;

        return $this;
    }
}
