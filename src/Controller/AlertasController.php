<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlertasController extends AbstractController
{
    /**
     * @Route("/alertas", name="alertas_index")
     */
    public function index(): Response
    {
        return $this->render('alertas/index.html.twig', [
            'controller_name' => 'AlertasController',
        ]);
    }
}
