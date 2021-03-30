<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Productos;
use App\Entity\Marcas;
use App\Repository\ProductosRepository;
use App\Repository\MarcasRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @Route("/importador")
 */
class ImportadorController extends AbstractController
{
    /**
     * @Route("/", name="importador_index", methods={"GET", "POST"})
     */
    public function index(): Response
    {
        return $this->render('importador/index.html.twig');
    }

    /**
     * @Route("/actualizar", name="importador_actualizar", methods={"POST"})
     */
    public function actualizar(Request $request): Response
    {
        $mensaje = [
            'tipo' => 'ERROR',
            'mensaje' => 'Se produjo un error guardando el archivo CSV'
        ];

        if ($this->isCsrfTokenValid('nuevo_csv', $request->request->get('_token'))) {
            $csv = $this->uploadCsv($request->files->get('importador')['archivo']);

            if($csv['estado'] == 'OK'){
                $csvParseado = $this->parseCsv($csv);
                if($csvParseado['estado'] == 'OK'){
                    $actualizacion = $this->actualizarProductos($csvParseado['csv'], array_keys($request->request->get('actualizar')));
                }
            }
        }

        return $this->render('importador/index.html.twig', [
            'mensaje' => $mensaje,
        ]);
    }

    public function uploadCsv($archivo){
        $ubicacion = $this->getParameter('kernel.project_dir').'/public/csv/';
        
        $ahora = new \DateTime();
        $nombre = $ahora->format('U').'.'.$archivo->guessExtension();
        $return = [
            'estado' => 'OK',
            'mensaje' => '',
            'archivo' => $nombre, 
            'ubicacion' => $ubicacion
        ];

        try {
            $archivo->move($ubicacion, $nombre);
        } catch (FileException $e) {
            $return['estado'] = 'ERROR';
            $return['mensaje'] = 'Se produjo un error guardando el archivo CSV';
            $return['error'] = $e;
        }

        return $return;
    }

    public function parseCsv($csv){
        $return = ['estado' => 'ERROR', 'mensaje' => 'Se produjo un error leyendo el archivo CSV', 'csv' => $csv['archivo']];

        if(is_dir($csv['ubicacion'])){
            $filesystem = new Filesystem;
            if($filesystem->exists('csv/'.$csv['archivo'])){
                $finder = new Finder();
                $finder->name($csv['archivo'])->in($csv['ubicacion']);
                $archivo = iterator_to_array($finder, false);
                $archivo = $archivo[0];
                $csvParseado = array_map('str_getcsv', file($archivo->getPathname()));

                $return = ['estado' => 'OK', 'mensaje' => '', 'csv' => $csvParseado];
            }
        }

        return $return;
    }

    public function actualizarProductos($csv, $actualizaciones){

        $entityManager = $this->getDoctrine()->getManager();
        $Productos = $entityManager->getRepository(Productos::class);
        
        foreach($csv as $p){
            if($p[0] !== null){
                $prod = $Productos->findOneBy(['id_externo' => $p[0]]);
                
                if(empty($prod)){
                    if(in_array('nuevos', $actualizaciones))
                        $this->nuevoProducto($p);
                }else{
                    if(in_array('precios', $actualizaciones))
                        $prod->setPrecio(floatval($p[2]));
                    if(in_array('nombres', $actualizaciones))
                        $prod->setNombre($p[1]);
                    if(in_array('marcas', $actualizaciones))
                        $prod->setMarca($this->validacionMarcas($p[3]));

                    $entityManager->flush();
                }
            }
        }
    }

    public function validacionMarcas($txtMarca){
        $entityManager = $this->getDoctrine()->getManager();
        $marcasRepo = $entityManager->getRepository(Marcas::class);
        $marca = $marcasRepo->findMarcaLike($txtMarca);

        if($marca === false){
            $marca = new Marcas;
            $marca->setMarca($txtMarca);
            $entityManager->persist($marca);
            $entityManager->flush();
        }else{
            $marca = $marcasRepo->find($marca['id']);
        }

        return $marca;
    }

    public function nuevoProducto($p){
        $entityManager = $this->getDoctrine()->getManager();
        $marca = $this->validacionMarcas($p[3]);

        $prod = new Productos;
        $prod->setIdExterno(intval($p[0]));
        $prod->setNombre($p[1]);
        $prod->setPrecio(floatval($p[2]));
        $prod->setMarca($marca);
        $prod->setHabilitado(1);
        $prod->setEliminado(0);
        $entityManager->persist($prod);
        $entityManager->flush();
    }
}
