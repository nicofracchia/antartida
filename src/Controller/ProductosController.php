<?php

namespace App\Controller;

use App\Entity\Productos;
use App\Entity\Categorias;
use App\Entity\Marcas;
use App\Entity\ProductosCategorias;
use App\Entity\ProductosCaracteristicas;
use App\Entity\ProductosImagenes;
use App\Repository\ProductosRepository;
use App\Repository\ProductosImagenesRepository;
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

            $id_externo = intval($request->request->get('producto')['id_externo']);
            $precio = (is_numeric($request->request->get('producto')['precio'])) ? $request->request->get('producto')['precio'] : 0;

            $producto = new Productos();
            $producto->setIdExterno($id_externo);
            $producto->setNombre($request->request->get('producto')['nombre']);
            $producto->setPrecio($precio);
            $producto->setDescripcion($request->request->get('producto')['descripcion']);
            $producto->setMarca($entityManager->getRepository(Marcas::class)->find($request->request->get('producto')['marca']));
            $producto->setHabilitado($request->request->get('producto')['habilitado'] ?? 0);

            $entityManager->persist($producto);
            $entityManager->flush();

            $this->guardaProductosCategorias($producto, $request->request->get('categoriasProducto'));

            $this->guardaProductosCaracteristicas($producto, $request->request->get('caracteristicasClave'), $request->request->get('caracteristicasValor'));

            return $this->redirectToRoute('productos_index');
        }

        return $this->render('productos/new.html.twig', [
            'categoriasAsignadas' => $this->categoriasPorProducto(0),
            'caracteristicas' => Array(),
            'marcas' => $entityManager->getRepository(Marcas::class)->findBy([], ['marca' => 'ASC'])
        ]);
    }

    /**
     * @Route("/{id}/editar", name="productos_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Productos $producto): Response
    {
        if($producto->getEliminado() == 1)
            return $this->redirectToRoute('productos_index');

        $entityManager = $this->getDoctrine()->getManager();

        if ($this->isCsrfTokenValid('editar_producto_'.$producto->getId(), $request->request->get('_token'))) {

            $id_externo = intval($request->request->get('producto')['id_externo']);
            $precio = (is_numeric($request->request->get('producto')['precio'])) ? $request->request->get('producto')['precio'] : 0;

            $producto->setIdExterno($id_externo);
            $producto->setNombre($request->request->get('producto')['nombre']);
            $producto->setPrecio($precio);
            $producto->setDescripcion($request->request->get('producto')['descripcion']);
            $producto->setMarca($entityManager->getRepository(Marcas::class)->find($request->request->get('producto')['marca']));
            $producto->setHabilitado($request->request->get('producto')['habilitado'] ?? 0);

            $entityManager->flush();

            $this->guardaProductosCategorias($producto, $request->request->get('categoriasProducto'));

            $this->guardaProductosCaracteristicas($producto, $request->request->get('caracteristicasClave'), $request->request->get('caracteristicasValor'));

            return $this->redirectToRoute('productos_index');
        }

        return $this->render('productos/edit.html.twig', [
            'producto' => $producto,
            'categoriasAsignadas' => $this->categoriasPorProducto($producto->getId()),
            'caracteristicas' => $entityManager->getRepository(ProductosCaracteristicas::class)->findBy(['producto' => $producto]),
            'marcas' => $entityManager->getRepository(Marcas::class)->findBy([], ['marca' => 'ASC'])
        ]);
    }

    /**
     * @Route("/eliminar/{id}", name="productos_delete", methods={"POST"})
     */
    public function delete(Request $request, Productos $producto): Response
    {
        $return = [
            'estado' => 'ERROR',
            'mensaje' => 'Token inv??lido.',
            'token_recibido' => $request->request->get('_token')
        ];

        if ($this->isCsrfTokenValid('delete_'.$producto->getId(), $request->request->get('_token'))) {
            $producto->setEliminado(1);
            $this->getDoctrine()->getManager()->flush();

            $return = [
                'estado' => 'OK',
                'mensaje' => 'El producto fue eliminado correctamente.'
            ];
        }

        return $this->json($return);
    }


    // CATEGORIAS 

    public function categoriasPorProducto($idProducto = 0){
        $return = ['ca' => [], 'cajs' => ''];
        
        if($idProducto != 0){
            $entityManager = $this->getDoctrine()->getManager();
            
            $categoriasAsignadas = $entityManager->getRepository(ProductosCategorias::class)->findBy(['producto' => $idProducto]);
            $i = 0;
            foreach($categoriasAsignadas as $ca){
                $return['ca'][$i]['id'] = $ca->getCategoria()->getId();
                $return['ca'][$i]['categoria'] = $ca->getCategoria()->getNombre();
                $return['cajs'] .= $ca->getCategoria()->getId().',';
                $i++;
            }
        }

        return $return;

    }

    public function guardaProductosCategorias($producto, $categorias){
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->getRepository(ProductosCategorias::class)->eliminarPorProducto($producto->getId());

        if($producto !== null and $categorias !== null){
            foreach ($categorias as $idCategoria){
                $categoria = $entityManager->getRepository(Categorias::class)->find($idCategoria);

                $prodCat = new ProductosCategorias();
                $prodCat->setProducto($producto);
                $prodCat->setCategoria($categoria);
                $entityManager->persist($prodCat);
                $entityManager->flush();
            }
        }
    }

    // CARACTERISTICAS

    public function guardaProductosCaracteristicas($producto, $claves, $valores){
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->getRepository(ProductosCaracteristicas::class)->eliminarPorProducto($producto->getId());

        for($i = 0; $i < count($claves); $i++){
            if($claves[$i] != '' and $valores[$i] != ''){
                $prodCar = new ProductosCaracteristicas;
                $prodCar->setProducto($producto);
                $prodCar->setClave($claves[$i]);
                $prodCar->setValor($valores[$i]);

                $entityManager->persist($prodCar);
                $entityManager->flush();
            }
        }

    }

    // MARCAS

    /**
     * @Route("/marcas", name="marcas_modal", methods={"GET","POST"})
     */
    public function modalMarcas(Request $request): Response
    {
        if ($this->isCsrfTokenValid('nueva_marca', $request->request->get('token'))) {
            
            $entityManager = $this->getDoctrine()->getManager();

            $marca = new Marcas();
            $marca->setMarca($request->request->get('marca'));

            $entityManager->persist($marca);
            $entityManager->flush();
            
            $r = Array();
            $r['id'] = $marca->getId();
            $r['marca'] = $marca->getMarca();

            return $this->json($r);
        }
        return $this->render('modal/modalMarca.html.twig');
    }

    // IMAGENES

    /**
     * @Route("/imagenes/{id}", name="productos_imagenes", methods={"GET", "POST"})
     */
    public function imagenes(Productos $producto, ProductosImagenesRepository $ProductosImagenesRepository): Response
    {
        return $this->render('productos/imagenes.html.twig', [
            'imagenes' => $ProductosImagenesRepository->findBy(['producto' => $producto],['habilitado' => 'DESC', 'orden' => 'ASC']),
            'producto' => $producto
        ]);
    }

    /**
     * @Route("/imagenes/{id}/nuevo", name="productos_imagenes_nuevo", methods={"POST"})
     */
    public function nuevaImagen(Productos $producto, Request $request, ProductosRepository $productosRepository): Response
    {
        if ($this->isCsrfTokenValid('nueva_imagen_producto_'.$producto->getId(), $request->request->get('_token'))) {
            
            $entityManager = $this->getDoctrine()->getManager();

            // SUBO IMAGEN
            $ubicacion = $this->getParameter('kernel.project_dir').'/public/images/productos/'.$producto->getId().'/';
            $file = $request->files->get('imagenes')['imagen'];
            $ahora = new \DateTime();
            $nombre = $ahora->format('U').'.'.$file->guessExtension();
            $file->move($ubicacion, $nombre);

            // GUARDO IMAGEN DE PRODUCTO
            $productosImagenes = new ProductosImagenes;
            $productosImagenes->setProducto($producto);
            $productosImagenes->setOrden(intval($request->request->get('imagenes')['orden']));
            $productosImagenes->setImagen($nombre);
            $productosImagenes->setHabilitado(1);
            $entityManager->persist($productosImagenes);
            $entityManager->flush();
            
        }

        return $this->redirectToRoute('productos_imagenes', ['id' => $producto->getId()]);
    }

    /**
     * @Route("/imagenes/editar/{id}", name="productos_imagenes_editar", methods={"POST"})
     */
    public function editarImagen(ProductosImagenes $ProductosImagenes, Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $ProductosImagenes->setOrden($request->request->get('orden'));
        $entityManager->flush();
        
        return $this->redirectToRoute('productos_imagenes', ['id' => $ProductosImagenes->getProducto()->getId()]);
    }

    /**
     * @Route("/imagenes/deshabilitar/{id}", name="productos_imagenes_deshabilitar", methods={"GET"})
     */
    public function deshabilitar(ProductosImagenes $ProductosImagenes): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $ProductosImagenes->setHabilitado(0);
        $entityManager->flush();
        
        return $this->redirectToRoute('productos_imagenes', ['id' => $ProductosImagenes->getProducto()->getId()]);
    }

    /**
     * @Route("/imagenes/habilitar/{id}", name="productos_imagenes_habilitar", methods={"GET"})
     */
    public function habilitar(ProductosImagenes $ProductosImagenes): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $ProductosImagenes->setHabilitado(1);
        $entityManager->flush();
        
        return $this->redirectToRoute('productos_imagenes', ['id' => $ProductosImagenes->getProducto()->getId()]);
    }

    /**
     * @Route("/imagenes/eliminar/{id}", name="productos_imagenes_eliminar", methods={"GET"})
     */
    public function eliminar(ProductosImagenes $ProductosImagenes): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $idProducto = $ProductosImagenes->getProducto()->getId();

        $ubicacion = $this->getParameter('kernel.project_dir').'/public/images/productos/'.$idProducto.'/';
        $nombre = $ProductosImagenes->getImagen();
        unlink($ubicacion.$nombre);
        
        $entityManager->remove($ProductosImagenes);
        $entityManager->flush();
        
        return $this->redirectToRoute('productos_imagenes', ['id' => $idProducto]);
    }
}