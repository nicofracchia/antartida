<?php

namespace App\Repository;

use App\Entity\ProductosCaracteristicas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductosCaracteristicas|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductosCaracteristicas|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductosCaracteristicas[]    findAll()
 * @method ProductosCaracteristicas[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductosCaracteristicasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductosCaracteristicas::class);
    }

    public function eliminarPorProducto($idProducto){
        $conn = $this->getEntityManager()->getConnection();

        $SQL  = "DELETE FROM productos_caracteristicas WHERE producto_id = :idProducto";

        $STMT = $conn->prepare($SQL);
        
        $STMT->execute(['idProducto' => $idProducto]);
    }
}
