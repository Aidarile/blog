<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Visita;
use App\Repository\RestauranteRepository;
use App\Repository\VisitaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Length;

class VisitaController extends AbstractController
{
    #[Route("/visita/form", name: "crear_visita", methods: ["GET", "POST"])]
    public function crearVisita(EntityManagerInterface $emi, RestauranteRepository $restauranteRepo, Request $request): Response
    {
        $visita = new Visita();

        $formulario = $this->createFormBuilder($visita)
            ->add("Restaurante", TextType::class, [
                "mapped" => false,
                "constraints" => [
                    new Length(["min" => 1, "max" => 256]),
                    new NotBlank()
                ],
                "attr" => ["placeholder" => "Nombre del Restaurante"]
            ])
            ->add("Valoracion", IntegerType::class, [
                "constraints" => [
                    new Range(["min" => 1, "max" => 10]),
                    new NotBlank()
                ],
                "attr" => ["placeholder" => "10"]
            ])
            ->add("Comentario", TextType::class, [
                "constraints" => [
                    new Length(["min" => 1, "max" => 256]),
                    new NotBlank()
                ],
                "attr" => ["placeholder" => "Escribe tu comentario"]
            ])
            ->add("Guardar", SubmitType::class)
            ->getForm();

        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            // Aquí obtenemos los datos del formulario
            $restauranteNombre = $formulario->get("Restaurante")->getData();
            $restaurante = $restauranteRepo->findOneBy(["Nombre" => $restauranteNombre]);

            if ($restaurante !== null) {
                $visita->setRestaurante($restaurante);
                $visita->setValoracion($formulario->get("Valoracion")->getData());
                $visita->setComentario($formulario->get("Comentario")->getData());

                // Persistimos la visita
                $emi->persist($visita);
                $emi->flush();

                return $this->redirectToRoute("mostrartodos_visita");
            } else {
                $this->addFlash("error", "Debes escribir un Restaurante que exista");
            }
        }

        return $this->render("visita/crearVisita.html.twig", [
            "formulario" => $formulario->createView()
        ]);
    }

    #[Route("/visita/formRestaurante/{idRestaurante}", name: "crear_visita_con_restaurante", methods: ["GET", "POST"])]
    public function crearVisitaPorRestaurante(EntityManagerInterface $emi, RestauranteRepository $restauranteRepo, int $idRestaurante, Request $request): Response
    {
        $visita = new Visita();
        $restaurante = $restauranteRepo->find($idRestaurante);

        $formulario = $this->createFormBuilder($visita)
            ->add("Restaurante", TextType::class, [
                "mapped" => false,
                "data" => $restaurante ? $restaurante->getNombre() : '',
                "constraints" => [
                    new Length(["min" => 1, "max" => 256]),
                    new NotBlank()
                ],
                "attr" => ["placeholder" => "Nombre del Restaurante"]
            ])
            ->add("Valoracion", IntegerType::class, [
                "constraints" => [
                    new Range(["min" => 1, "max" => 10]),
                    new NotBlank()
                ],
                "attr" => ["placeholder" => "10"]
            ])
            ->add("Comentario", TextType::class, [
                "constraints" => [
                    new Length(["min" => 1, "max" => 256]),
                    new NotBlank()
                ],
                "attr" => ["placeholder" => "Escribe tu comentario"]
            ])
            ->add("Guardar", SubmitType::class)
            ->getForm();

        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            $visita->setRestaurante($restaurante);
            $emi->persist($visita);
            $emi->flush();

            return $this->redirectToRoute("mostrartodos_visita");
        }

        $this->addFlash("error", "Debes poner un Restaurante que exista");

        return $this->render("visita/crearVisitaConRestaurante.html.twig", [
            "formulario" => $formulario->createView()
        ]);
    }

    #[Route("/visita", name: "mostrartodos_visita", methods: ["GET"])]
    public function listaVisitas(VisitaRepository $repo): Response
    {
        $visitas = $repo->findAll();

        return $this->render("visita/index.html.twig", [
            "controller_name" => "Lista de Visitas",
            "listadoVisitas" => $visitas
        ]);
    }

    #[Route("/visita/form/{idVisita}", name: "actualizar_visita", methods: ["GET", "POST"])]
    public function actualizarVisita(EntityManagerInterface $emi, RestauranteRepository $restauranteRepo, VisitaRepository $repo, int $idVisita, Request $request): Response
    {
        $visita = $repo->find($idVisita);

        $formulario = $this->createFormBuilder($visita)
            ->add("Restaurante", TextType::class, [
                "mapped" => false,
                "data" => $visita->getRestaurante() ? $visita->getRestaurante()->getNombre() : '',
                "constraints" => [
                    new Length(["min" => 1, "max" => 256]),
                    new NotBlank()
                ],
                "attr" => ["placeholder" => "Nombre del Restaurante"]
            ])
            ->add("Valoracion", IntegerType::class, [
                "constraints" => [
                    new Range(["min" => 1, "max" => 10]),
                    new NotBlank()
                ],
                "attr" => ["placeholder" => "10"]
            ])
            ->add("Comentario", TextType::class, [
                "constraints" => [
                    new Length(["min" => 1, "max" => 256]),
                    new NotBlank()
                ],
                "attr" => ["placeholder" => "Escribe tu comentario"]
            ])
            ->add("Guardar", SubmitType::class)
            ->getForm();

        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            $restauranteNombre = $formulario->get("Restaurante")->getData();
            $restaurante = $restauranteRepo->findOneBy(["Nombre" => $restauranteNombre]);

            if ($restaurante !== null) {
                $visita->setRestaurante($restaurante);
                $emi->flush();

                return $this->redirectToRoute("mostrartodos_visita");
            }

            $this->addFlash("error", "Restaurante no encontrado");
        }

        return $this->render("visita/actualizarVisita.html.twig", [
            "formulario" => $formulario->createView()
        ]);
    }

    #[Route("/visita/{idVisita}", name: "eliminar_visita", methods: ["POST"])]
    public function eliminarVisita(EntityManagerInterface $emi, VisitaRepository $repo, int $idVisita): Response
    {
        $visita = $repo->find($idVisita);

        if ($visita) {
            $emi->remove($visita);
            $emi->flush();
        }

        return $this->redirectToRoute("mostrartodos_visita");
    }
}
