<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Banners;
use App\Repository\BannersRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/banners")
 */
class BannersController extends AbstractController
{
    /**
     * @Route("/", name="banners_index")
     */
    public function index(BannersRepository $bannersRepository): Response
    {
        return $this->render('banners/index.html.twig', [
            'banners' => $bannersRepository->findBy([],['orden' => 'ASC']),
        ]);
    }

    /**
     * @Route("/new", name="banners_nuevo", methods={"POST"})
     */
    public function new(Request $request, BannersRepository $bannersRepository): Response
    {
        return $this->render('banners/index.html.twig', [
            'banners' => $bannersRepository->findBy([],['orden' => 'ASC']),
        ]);
    }
}
