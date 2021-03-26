<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

class ImportadorController extends AbstractController
{
    /**
     * @Route("/importador", name="importador", methods={"GET", "POST"})
     */
    public function index(): Response
    {
        return $this->render('importador/index.html.twig');
    }

    /**
     * @Route("/importador/actualizar", name="importador_actualizar", methods={"POST"})
     */
    public function actualizar(Request $request): Response
    {
        $mensaje = [
            'tipo' => 'ERROR',
            'mensaje' => 'Se produjo un error guardando el archivo CSV'
        ];

        if ($this->isCsrfTokenValid('nuevo_csv', $request->request->get('_token'))) {
            $csv = $this->uploadCsv($request->files->get('importador')['archivo']);
            
            if(is_dir($csv['ubicacion'])){
                $filesystem = new Filesystem;
                if($filesystem->exists('csv/'.$csv['archivo'])){
                    $finder = new Finder();
                    $finder->name($csv['archivo'])->in($csv['ubicacion']);
                    $archivo = iterator_to_array($finder, false);
                    $archivo = $archivo[0];
                    $archivoNombre = $archivo->getRelativePathname();
                }
            }

            dump($archivo->getContents());
            dump($archivoNombre);

            exit();
        }

        return $this->render('importador/index.html.twig', [
            'mensaje' => $mensaje,
        ]);
    }

    public function uploadCsv($archivo){
        $ubicacion = $this->getParameter('kernel.project_dir').'/public/csv/';
        
        $ahora = new \DateTime();
        $nombre = $ahora->format('U').'.'.$archivo->guessExtension();

        $return = ['archivo' => $nombre, 'ubicacion' => $ubicacion, 'error' => '0'];

        try {
            $archivo->move($ubicacion, $nombre);
        } catch (FileException $e) {
            $return['error'] = $e;
        }

        return $return;
    }
}
