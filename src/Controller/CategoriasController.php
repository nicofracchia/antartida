<?php

namespace App\Controller;

use App\Entity\Categorias;
use App\Repository\CategoriasRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/categorias")
 */
class CategoriasController extends AbstractController
{
    /**
     * @Route("/", name="categorias_index", methods={"GET"})
     */
    public function index(CategoriasRepository $categoriasRepository): Response
    {
        return $this->render('categorias/index.html.twig');
    }

    /**
     * @Route("/listado", name="categorias_listado", methods={"GET", "POST"})
     */
    public function listado(CategoriasRepository $categoriasRepository): Response
    {
        $categorias = Array();
        $i = 0;
        foreach($categoriasRepository->findBy(array('eliminado' => 0), array('padre' => 'ASC', 'nombre' => 'ASC')) as $c){
            $categorias[$i]['id'] = $c->getId();
            $categorias[$i]['nombre'] = $c->getNombre();
            $categorias[$i]['idPadre'] = $c->getPadre();
            $i++;
        }

        return $this->json($categorias);
    }

    /**
     * @Route("/new", name="categorias_nueva", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        if ($this->isCsrfTokenValid('nueva_categoria', $request->request->get('_token'))) {
            $padre = $entityManager->getRepository(Categorias::class)->find($request->request->get('padre'));
            $categoria = new Categorias();
            $categoria->setNombre($request->request->get('categoria'));
            $categoria->setPadre($request->request->get('padre'));
            $categoria->setGrupo($padre->getGrupo().'|'.$request->request->get('padre'));
            $categoria->setHabilitado(1);
            $categoria->setEliminado(0);

            $entityManager->persist($categoria);
            $entityManager->flush();

            return $this->json($categoria);
        }
    }

    /**
     * @Route("/eliminar", name="categorias_delete", methods={"POST"})
     */
    public function delete(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        if ($this->isCsrfTokenValid('eliminar_categoria', $request->request->get('eliminar_categoria'))) {
            $categoria = $entityManager->getRepository(Categorias::class)->eliminarArbol($request->request->get('id'));

            return $this->json($categoria);
        }
    }
}
