<?php

namespace App\Entity;

use App\Repository\UserGroupsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserGroupsRepository::class)]
class UserGroups
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(targetEntity: Note::class, mappedBy: 'userGroups')]
    private Collection $note;

    #[ORM\OneToMany(targetEntity: UserPermission::class, mappedBy: 'userGroups', cascade: ["REMOVE"])]
    private Collection $userPermissions;



    public function __construct()
    {
        $this->note = new ArrayCollection();
        $this->userPermissions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Note>
     */
    public function getNote(): Collection
    {
        return $this->note;
    }

    public function addNote(Note $note): static
    {
        if (!$this->note->contains($note)) {
            $this->note->add($note);
            $note->setUserGroups($this);
        }

        return $this;
    }

    public function removeNote(Note $note): static
    {
        if ($this->note->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getUserGroups() === $this) {
                $note->setUserGroups(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, UserPermission>
     */
    public function getUserPermission(): Collection
    {
        return $this->userPermissions;
    }

    public function addUserPermission(UserPermission $userPermission): static
    {
        if (!$this->userPermissions->contains($userPermission)) {
            $this->userPermissions->add($userPermission);
            $userPermission->setUserGroups($this);
        }

        return $this;
    }

    public function removeUserPermission(UserPermission $userPermission): static
    {
        if ($this->userPermissions->removeElement($userPermission)) {
            // set the owning side to null (unless already changed)
            if ($userPermission->getUserGroups() === $this) {
                $userPermission->setUserGroups(null);
            }
        }

        return $this;
    }
}
