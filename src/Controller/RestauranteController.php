<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\Restaurante;
use App\Repository\RestauranteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class RestauranteController extends AbstractController
{
    #[Route('/restaurante', name: 'listado_restaurantes', methods: ["GET"])]
    public function listaRestaurantes(RestauranteRepository $repo): Response 
    {
        $listadoRestaurantes = $repo->findAll();

        return $this->render('restaurante/index.html.twig', [
            "controller_name" => "Esto muestra todos los restaurantes", 
            "listadoRestaurantes" => $listadoRestaurantes
        ]);
    }

    #[Route('/restaurante/form', name: 'crear_restaurante', methods: ["GET", "POST"])]
    public function crearRestaurante(EntityManagerInterface $emi, Request $request): Response 
    {
        $restaurante = new Restaurante();

        $fb = $this->createFormBuilder($restaurante)
            ->add("Nombre", TextType::class, [
                "constraints" => [
                    new Length(["min" => 1, "max" => 256]),
                    new NotBlank()
                ],
                'attr' => [
                    'placeholder' => 'Nombre del Restaurante'
                ]
            ])
            ->add("Direccion", TextType::class, [
                "constraints" => [
                    new Length(["min" => 1, "max" => 256]),
                    new NotBlank()
                ],
                'attr' => [
                    'placeholder' => 'Direccion del Restaurante'
                ]
            ])
            ->add("Telefono", TextType::class, [
                "required" => false,
                "constraints" => [
                    new Length(["min" => 9, "max" => 12])
                ],
                'attr' => [
                    'placeholder' => 'Telefono del Restaurante'
                ]
            ])
            ->add("Tipo_de_cocina", TextType::class, [
                "required" => false,
                "constraints" => [
                    new Length(["min" => 1, "max" => 256])
                ],
                'attr' => [
                    'placeholder' => 'Tipo de cocina del Restaurante'
                ]
            ])
            ->add("GUARDAR", SubmitType::class);

        $formulario = $fb->getForm();
        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            $restaurante = $formulario->getData();
            $emi->persist($restaurante);
            $emi->flush();

            return $this->redirectToRoute("listado_restaurantes");
        }

        return $this->render("restaurante/crearRestaurante.html.twig", [
            "formulario" => $formulario
        ]);
    }

    #[ Route("/restaurante/{idRestaurante}", name: 'mostrar_restaurante', methods: ["GET"])]
    public function mostrarRestaurante (RestauranteRepository $repo, int $idRestaurante): Response{

	$restaurante = $repo->find($idRestaurante);


    return $this->render('restaurante/mostrarRestaurante.html.twig', [
        "controller_name" => "Detalles del Restaurante seleccionado",
         "restaurante" => $restaurante
        ]);
	}

    #[Route('/restaurante/form/{idRestaurante}', name: 'actualizar_restaurante', methods: ["GET", "POST"])]
    public function actualizarRestaurante(RestauranteRepository $repo, EntityManagerInterface $emi, int $idRestaurante, Request $request): Response 
    {
        $restaurante = $repo->find($idRestaurante);

        $fb = $this->createFormBuilder($restaurante)
            ->add('Nombre', TextType::class, [
                "constraints" => [
                    new Length(["min" => 1, "max" => 256]),
                    new NotBlank()
                ],
                'attr' => [
                    'placeholder' => 'Nombre del Restaurante'
                ]
            ])
            ->add("Direccion", TextType::class, [
                "constraints" => [
                    new Length(["min" => 1, "max" => 256]),
                    new NotBlank()
                ],
                'attr' => [
                    'placeholder' => 'Dirección del Restaurante'
                ]
            ])
            ->add("Telefono", TextType::class, [
                "required" => false,
                "constraints" => [
                    new Length(["min" => 9, "max" => 12])
                ],
                'attr' => [
                    'placeholder' => 'Telefono del restaurante'
                ]
            ])
            ->add("Tipo_de_cocina", TextType::class, [
                "required" => false,
                "constraints" => [
                    new Length(["min" => 1, "max" => 256])
                ],
                'attr' => [
                    'placeholder' => 'Tipo de cocina del Restaurante'
                ]
            ])
            ->add('Guardar', SubmitType::class, [
                'label' => 'GUARDAR',
            ]);

        $formulario = $fb->getForm();
        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            $emi->flush();

            return $this->redirectToRoute("listado_restaurantes");
        }

        return $this->render("restaurante/actualizarRestaurante.html.twig", [
            "formulario" => $formulario
        ]);
    }

    #[Route('/restaurante/{idRestaurante}', name: 'eliminar_restaurante', methods: ["POST"])]
public function eliminarRestaurante(RestauranteRepository $repo, EntityManagerInterface $emi, int $idRestaurante): Response 
{
    $restaurante = $repo->find($idRestaurante);

    if ($restaurante) {
        if (empty($restaurante->getVisitas()[0])) {
            $emi->remove($restaurante);
            $emi->flush();

            $this->addFlash('success', 'Restaurante eliminado con éxito.');
        } else {
            $this->addFlash('error', 'No puedes eliminar un restaurante con visitas activas, elimina las visitas primero.');
        }
    }

    return $this->redirectToRoute("listado_restaurantes");
}
}
