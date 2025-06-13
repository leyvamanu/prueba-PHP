<?php

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
class Employee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $position = null;

    /**
     * @var Collection<int, WorkLog>
     */
    #[ORM\OneToMany(targetEntity: WorkLog::class, mappedBy: 'employee')]
    private Collection $workLogs;

    public function __construct()
    {
        $this->workLogs = new ArrayCollection();
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): static
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return Collection<int, WorkLog>
     */
    public function getWorkLogs(): Collection
    {
        return $this->workLogs;
    }

    public function addWorkLog(WorkLog $workLog): static
    {
        if (!$this->workLogs->contains($workLog)) {
            $this->workLogs->add($workLog);
            $workLog->setEmployee($this);
        }

        return $this;
    }

    public function removeWorkLog(WorkLog $workLog): static
    {
        if ($this->workLogs->removeElement($workLog)) {
            // set the owning side to null (unless already changed)
            if ($workLog->getEmployee() === $this) {
                $workLog->setEmployee(null);
            }
        }

        return $this;
    }
}
