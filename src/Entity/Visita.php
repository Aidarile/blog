<?php

namespace App\Entity;

use App\Repository\VisitaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VisitaRepository::class)]
class Visita
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $Valoracion = null;

    #[ORM\Column(length: 256, nullable: true)]
    private ?string $Comentario = null;

    #[ORM\ManyToOne(inversedBy: 'visitas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Restaurante $Restaurante = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValoracion(): ?int
    {
        return $this->Valoracion;
    }

    public function setValoracion(int $Valoracion): static
    {
        $this->Valoracion = $Valoracion;

        return $this;
    }

    public function getComentario(): ?string
    {
        return $this->Comentario;
    }

    public function setComentario(?string $Comentario): static
    {
        $this->Comentario = $Comentario;

        return $this;
    }

    public function getRestaurante(): ?Restaurante
    {
        return $this->Restaurante;
    }

    public function setRestaurante(?Restaurante $Restaurante): static
    {
        $this->Restaurante = $Restaurante;

        return $this;
    }
}
