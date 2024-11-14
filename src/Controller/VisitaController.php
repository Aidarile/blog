<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\Visita;
use Doctrine\ORM\EntityManagerInterface;

class VisitaController extends AbstractController
{
    #[Route('/visita', name: 'app_visita')]
    public function index(): Response
    {
        return $this->render('visita/index.html.twig', [
            'controller_name' => 'VisitaController',
        ]);
    }
}
