<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MenuRepository::class)
 */
class Menu extends Produit
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Burger::class, inversedBy="menus")
     * @ORM\JoinColumn(nullable=false)
     */
    private $burger;

    /**
     * @ORM\ManyToMany(targetEntity=Complement::class)
     */
    private $complements;

    public function __construct()
    {
        parent::__construct();
        $this->complements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return parent::getId();
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function emptyComplements(){
        $this->complements= new ArrayCollection();
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getBurger(): ?Burger
    {
        return $this->burger;
    }

    public function setBurger(?Burger $burger): self
    {
        $this->burger = $burger;

        return $this;
    }

    /**
     * @return Collection|Complement[]
     */
    public function getComplements(): Collection
    {
        return $this->complements;
    }

    public function addComplement(Complement $complement): self
    {
        if (!$this->complements->contains($complement)) {
            $this->complements[] = $complement;
        }

        return $this;
    }

    public function removeComplement(Complement $complement): self
    {
        $this->complements->removeElement($complement);

        return $this;
    }
}
