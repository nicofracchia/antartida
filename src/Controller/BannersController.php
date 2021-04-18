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
            'banners' => $bannersRepository->findBy([],['habilitado' => 'DESC', 'orden' => 'ASC']),
        ]);
    }

    /**
     * @Route("/new", name="banners_nuevo", methods={"POST"})
     */
    public function new(Request $request, BannersRepository $bannersRepository): Response
    {
        if ($this->isCsrfTokenValid('nuevo_banner', $request->request->get('_token'))) {
            
            $entityManager = $this->getDoctrine()->getManager();

            // SUBO IMAGEN
            $ubicacion = $this->getParameter('kernel.project_dir').'/public/images/banners/';
            $file = $request->files->get('banners')['imagen'];
            $ahora = new \DateTime();
            $nombre = $ahora->format('U').'.'.$file->guessExtension();
            $file->move($ubicacion, $nombre);

            // GUARDO BANNER
            $banner = new Banners;
            $banner->setOrden($request->request->get('banners')['orden']);
            $banner->setImagen($nombre);
            $banner->setUrl($request->request->get('banners')['url']);
            $banner->setHabilitado(1);
            $entityManager->persist($banner);
            $entityManager->flush();
        }

        return $this->redirectToRoute('banners_index');
    }

    /**
     * @Route("/deshabilitar/{id}", name="banners_deshabilitar", methods={"GET"})
     */
    public function deshabilitar(Banners $banner): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $banner->setHabilitado(0);
        $entityManager->flush();
        
        return $this->redirectToRoute('banners_index');
    }

    /**
     * @Route("/habilitar/{id}", name="banners_habilitar", methods={"GET"})
     */
    public function habilitar(Banners $banner): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $banner->setHabilitado(1);
        $entityManager->flush();
        
        return $this->redirectToRoute('banners_index');
    }

    /**
     * @Route("/editar/{id}", name="banners_editar", methods={"POST"})
     */
    public function editar(Banners $banner, Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $banner->setOrden($request->request->get('orden'));
        $banner->setUrl($request->request->get('url'));
        $entityManager->flush();
        
        return $this->redirectToRoute('banners_index');
    }

    /**
     * @Route("/eliminar/{id}", name="banners_eliminar", methods={"GET"})
     */
    public function eliminar(Banners $banner): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $ubicacion = $this->getParameter('kernel.project_dir').'/public/images/banners/';
        $nombre = $banner->getImagen();
        unlink($ubicacion.$nombre);
        
        $entityManager->remove($banner);
        $entityManager->flush();
        
        return $this->redirectToRoute('banners_index');
    }
}
