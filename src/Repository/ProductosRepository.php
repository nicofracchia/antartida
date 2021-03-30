<?php

namespace App\Repository;

use App\Entity\Productos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Productos|null find($id, $lockMode = null, $lockVersion = null)
 * @method Productos|null findOneBy(array $criteria, array $orderBy = null)
 * @method Productos[]    findAll()
 * @method Productos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Productos::class);
    }

    public function findByFiltros($filtros, $orden){
        $conn = $this->getEntityManager()->getConnection();

        $params = Array();
        $params['eliminado'] = $filtros['eliminado'];

        $SQL  = "SELECT p.id, p.id_externo, p.nombre, p.precio, p.descripcion, p.habilitado, m.marca ";
        $SQL .= "FROM productos AS p ";
        $SQL .= "LEFT JOIN marcas AS m ";
        $SQL .= "   ON p.marca_id = m.id ";
        $SQL .= "WHERE eliminado = :eliminado ";

        if($filtros['marca'] != ''){
            $SQL .= " AND m.marca LIKE :marca ";
            $params['marca'] = '%'.$filtros['marca'].'%';
        }

        if($filtros['nombre'] != ''){
            $SQL .= " AND p.nombre LIKE :nombre ";
            $params['nombre'] = '%'.$filtros['nombre'].'%';
        }

        if($filtros['id_externo'] != ''){
            $SQL .= " AND p.id_externo LIKE :id_externo ";
            $params['id_externo'] = $filtros['id_externo'];
        }

        $SQL .= " ORDER BY ";
        $i = 0;
        foreach(array_keys($orden) as $k){
            $SQL .= ($i > 0) ? ", " : " ";
            $SQL .= $k." ".$orden[$k];
            $i++;
        }

        $STMT = $conn->prepare($SQL);
        
        $STMT->execute($params);

        return $STMT->fetchAllAssociative();
    }

    public function actualizarImportado($producto, $actualizaciones){

    }

    public function crearImportado($producto){
        
    }

    public function findSinCategorias(){
        $conn = $this->getEntityManager()->getConnection();

        $SQL  = "SELECT p.id, p.id_externo, p.nombre, p.precio, p.habilitado ";
        $SQL .= "FROM productos AS p ";
        $SQL .= "LEFT JOIN productos_categorias AS pc ";
        $SQL .= "    ON p.id = pc.producto_id ";
        $SQL .= "WHERE p.eliminado = 0 AND pc.categoria_id IS NULL ";
        $SQL .= "GROUP BY p.id ";
        $SQL .= "ORDER BY p.nombre ";

        $STMT = $conn->prepare($SQL);
        
        $STMT->execute();

        return $STMT->fetchAllAssociative();
    }

    public function findSinPrecio(){
        $conn = $this->getEntityManager()->getConnection();

        $SQL  = "SELECT p.id, p.id_externo, p.nombre, p.precio, p.habilitado ";
        $SQL .= "FROM productos AS p ";
        $SQL .= "WHERE p.eliminado = 0 AND p.precio = 0 ";
        $SQL .= "GROUP BY p.id ";
        $SQL .= "ORDER BY p.nombre ";

        $STMT = $conn->prepare($SQL);
        
        $STMT->execute();

        return $STMT->fetchAllAssociative();
    }
}
