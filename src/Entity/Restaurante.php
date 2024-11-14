<?php

namespace App\Entity;

use App\Repository\RestauranteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RestauranteRepository::class)]
class Restaurante
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 256)]
    private ?string $Nombre = null;

    #[ORM\Column(length: 256)]
    private ?string $Direccion = null;

    #[ORM\Column(length: 12, nullable: true)]
    private ?string $Telefono = null;

    #[ORM\Column(length: 256, nullable: true)]
    private ?string $Tipo_de_cocina = null;

    /**
     * @var Collection<int, Visita>
     */
    #[ORM\OneToMany(targetEntity: Visita::class, mappedBy: 'Restaurante', orphanRemoval: true)]
    private Collection $visitas;

    public function __construct()
    {
        $this->visitas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->Nombre;
    }

    public function setNombre(string $Nombre): static
    {
        $this->Nombre = $Nombre;

        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->Direccion;
    }

    public function setDireccion(string $Direccion): static
    {
        $this->Direccion = $Direccion;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->Telefono;
    }

    public function setTelefono(?string $Telefono): static
    {
        $this->Telefono = $Telefono;

        return $this;
    }

    public function getTipoDeCocina(): ?string
    {
        return $this->Tipo_de_cocina;
    }

    public function setTipoDeCocina(?string $Tipo_de_cocina): static
    {
        $this->Tipo_de_cocina = $Tipo_de_cocina;

        return $this;
    }

    /**
     * @return Collection<int, Visita>
     */
    public function getVisitas(): Collection
    {
        return $this->visitas;
    }

    public function addVisita(Visita $visita): static
    {
        if (!$this->visitas->contains($visita)) {
            $this->visitas->add($visita);
            $visita->setRestaurante($this);
        }

        return $this;
    }

    public function removeVisita(Visita $visita): static
    {
        if ($this->visitas->removeElement($visita)) {
            // set the owning side to null (unless already changed)
            if ($visita->getRestaurante() === $this) {
                $visita->setRestaurante(null);
            }
        }

        return $this;
    }
}
