<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AirController extends AbstractController
{
    #[Route('/', name: 'availability')]
    public function availability(): Response
    {
        // TODO: La lóica de negocio debe de estar separada en el servicio aéreo.

        return $this->render('air/availability.html.twig', [
            'controller_name' => 'AirController',
        ]);
    }
}
