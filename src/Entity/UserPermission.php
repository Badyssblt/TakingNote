<?php

namespace App\Entity;

use App\Repository\UserPermissionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserPermissionRepository::class)]
class UserPermission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $role = [];

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'userPermissions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserGroups $userGroups;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRole(): array
    {
        return $this->role;
    }

    public function setRole(array $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getUserGroups(): ?UserGroups
    {
        return $this->userGroups;
    }

    public function setUserGroups(?UserGroups $userGroups): static
    {
        $this->userGroups = $userGroups;

        return $this;
    }
}
