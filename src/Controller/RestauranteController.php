<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\Restaurante;
use App\Repository\RestauranteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\Length;


class RestauranteController extends AbstractController
{
    #[Route('/restaurante', name: 'listado_restaurantes', methods: ["GET"])]
    public function listaRestaurantes(RestauranteRepository $repo): Response {

        $listadoRestaurantes = $repo -> findAll();

        return $this -> render('restaurante/index.html.twig', ["controller_name" => "Esto muestra todos los restaurantes", 
        "listadoRestaurantes" => $listadoRestaurantes]);    
    }

    #[Route('/restaurante/form', name: 'crear_restaurante', methods: ["GET", "POST"])]
    public function crearRestaurante(EntityManagerInterface $emi, Request $request): Response {

        $restaurante = new Restaurante();

        $fb = $this -> createFormBuilder($restaurante);

        $fb->add("Nombre", TextType::class, [

            "constraints"=>[
                new Length(["min"=>1,"max"=> 255]),
                new NotBlank()
            ]
        ]);
        $fb->add("Direccion", TextType::class, [

            "constraints"=>[
                new Length(["min"=>1,"max"=> 255]),
                new NotBlank()
            ]
        ]);
        $fb->add("Telefono", TextType::class, [
            "required" => false,
            "constraints"=>[
                new Length(["min"=>1,"max"=> 12])
            ]
        ]);
        $fb->add("Tipo_de_cocina", TextType::class, [
            "required" => false,
            "constraints"=>[
                new Length(["min"=>1,"max"=> 255])
            ]
        ]);
        $fb->add("Guardar", SubmitType::class);

        $formulario = $fb -> getForm();

        $formulario -> handleRequest($request);

        if ($formulario -> isSubmitted() && $formulario -> isValid()){
            $restaurante = $formulario -> getData();

            $emi -> persist ($restaurante);
            $emi -> flush();
            
        return  $this -> redirectToRoute("listado_restaurantes");
        } else {
        return $this -> render("restaurante/crearRestaurante.html.twig", ["formulario" => $formulario]);
        }

       /* $restaurante -> setNombre("Burguer Lhorta");
        $restaurante -> setDireccion("Avenida Grande 32, Albal");
        $restaurante -> setTelefono("961234567");
        $restaurante -> setTipoDeCocina("Hamburgueseria");

        $emi -> persist ($restaurante);
        $emi -> flush();

        return new JsonResponse("El restaurante " .$restaurante->getNombre() . " con ID " . $restaurante-> getId() . " ha sido creado", Response::HTTP_CREATED);
        // RESPUESTA MÁS SENCILLA QUE LA DE ARRIBA: return new JsonResponse("El restaurante introducido ha sido creado");
        */
        }



    #[Route('/restaurante/{idRestaurante}', name: 'mostrar_restaurante', methods: ["GET"])]
    public function mostrarRestaurante(RestauranteRepository $repo, int $idRestaurante): Response {

        $restaurante = $repo -> find($idRestaurante);

        return $this -> render('restaurante/mostrarRestaurante.html.twig', ["controller_name" => "Este es tu restaurante", 
        "restaurante" => $restaurante]);    
    }
    

   
    #[Route('/restaurante/{idRestaurante}', name: 'modificar_restaurante', methods: ["PATCH"])]
    public function modificarRestaurante(RestauranteRepository $repo, EntityManagerInterface $emi, int $idRestaurante): Response {
        
        $restaurante = $repo -> find($idRestaurante);

        $restaurante -> setNombre("Bar Italiano");
        $restaurante -> setDireccion("Calle Dragon Rojo 33");
        
        $emi -> flush();

        return new JsonResponse("Dato actualizado", Response::HTTP_OK);
    }


    #[Route('/restaurante/{idRestaurante}', name: 'eliminar_restaurante', methods: ["DELETE"])]
    public function eliminarRestaurante(RestauranteRepository $repo, EntityManagerInterface $emi, int $idRestaurante): Response {
        
        $restaurante = $repo -> find($idRestaurante);

        $emi -> remove($restaurante);        
        $emi -> flush();

        return new JsonResponse("Restaurante eliminado", Response::HTTP_OK);
    }


    
}
