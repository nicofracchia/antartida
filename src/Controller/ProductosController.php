<?php

namespace App\Controller;

use App\Entity\Productos;
use App\Entity\Categorias;
use App\Entity\Marcas;
use App\Entity\ProductosCategorias;
use App\Repository\ProductosRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @Route("/productos")
 */
class ProductosController extends AbstractController
{
    /**
     * @Route("/", name="productos_index", methods={"GET", "POST"})
     */
    public function index(Request $request, ProductosRepository $productosRepository): Response
    {
        $filtros = [
            'marca' => (isset($request->request->get('filtroProductos')['marca'])) ? $request->request->get('filtroProductos')['marca'] : '',
            'nombre' => (isset($request->request->get('filtroProductos')['nombre'])) ? $request->request->get('filtroProductos')['nombre'] : '',
            'id_externo' => (isset($request->request->get('filtroProductos')['id_externo'])) ? $request->request->get('filtroProductos')['id_externo'] : '',
            'eliminado' => 0
        ];

        $orden = ['nombre' => 'ASC'];

        $productos = $productosRepository->findByFiltros($filtros, $orden);

        dump($productos);

        return $this->render('productos/index.html.twig', [
            'productos' => $productos,
            'filtrosAplicados' => $filtros
        ]);
    }

    /**
     * @Route("/nuevo", name="productos_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        if ($this->isCsrfTokenValid('nuevo_producto', $request->request->get('_token'))) {

            $id_externo = (is_int($request->request->get('producto')['id_externo'])) ? $request->request->get('producto')['id_externo'] : 0;
            $precio = (is_float($request->request->get('producto')['precio'])) ? $request->request->get('producto')['precio'] : 0;

            // GUARDO EL PRODUCTO Y LO USO PARA ASIGNAR ALMACENES
            $prod = new Productos();
            $prod->setIdExterno($id_externo);
            $prod->setNombre($request->request->get('producto')['nombre']);
            $prod->setPrecio($precio);
            $prod->setDescripcion($request->request->get('producto')['descripcion']);
            $prod->setMarca($entityManager->getRepository(Marcas::class)->find($request->request->get('producto')['marca']));
            $prod->setHabilitado($request->request->get('producto')['habilitado'] ?? 0);

            $entityManager->persist($prod);
            $entityManager->flush();

            /*
            // ASIGNO CATEGORIAS AL PRODUCTO NUEVO
            if($request->request->get('categoriasProducto') !== null){
                foreach ($request->request->get('categoriasProducto') as $idCategoria){
                    $cat = $entityManager->getRepository(Categorias::class)->find($idCategoria);
                    $CP = new CategoriasProductos();
                    $CP->setProducto($prod);
                    $CP->setCategoria($cat);
                    $entityManager->persist($CP);
                    $entityManager->flush();
                }
            }
            */

            return $this->redirectToRoute('productos_index');
        }

        return $this->render('productos/new.html.twig', [
            'marcas' => $entityManager->getRepository(Marcas::class)->findAll()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="productos_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Productos $producto): Response
    {
        $form = $this->createForm(ProductosType::class, $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('productos_index');
        }

        return $this->render('productos/edit.html.twig', [
            'producto' => $producto,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="productos_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Productos $producto): Response
    {
        if ($this->isCsrfTokenValid('delete'.$producto->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($producto);
            $entityManager->flush();
        }

        return $this->redirectToRoute('productos_index');
    }
}
