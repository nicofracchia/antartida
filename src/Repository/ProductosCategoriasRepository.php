<?php

namespace App\Repository;

use App\Entity\ProductosCategorias;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductosCategorias|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductosCategorias|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductosCategorias[]    findAll()
 * @method ProductosCategorias[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductosCategoriasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductosCategorias::class);
    }

    public function eliminarPorProducto($idProducto){
        $conn = $this->getEntityManager()->getConnection();

        $SQL  = "DELETE FROM productos_categorias WHERE producto_id = :idProducto";

        $STMT = $conn->prepare($SQL);
        
        $STMT->execute(['idProducto' => $idProducto]);
    }
}
