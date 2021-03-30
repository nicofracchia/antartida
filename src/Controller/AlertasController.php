<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ProductosRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlertasController extends AbstractController
{
    /**
     * @Route("/alertas", name="alertas_index")
     */
    public function index(ProductosRepository $productosRepository): Response
    {
        $productosSinCategorias = $productosRepository->findSinCategorias();

        $productosSinPrecio = $productosRepository->findSinPrecio();
        
        return $this->render('alertas/index.html.twig', [
            'productosSinCategorias' => $productosSinCategorias,
            'productosSinPrecio' => $productosSinPrecio
        ]);
    }
}
