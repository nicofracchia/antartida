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

    // /**
    //  * @return ProductosCategorias[] Returns an array of ProductosCategorias objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProductosCategorias
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
