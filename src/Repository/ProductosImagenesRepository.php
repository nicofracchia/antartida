<?php

namespace App\Repository;

use App\Entity\ProductosImagenes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductosImagenes|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductosImagenes|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductosImagenes[]    findAll()
 * @method ProductosImagenes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductosImagenesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductosImagenes::class);
    }

    // /**
    //  * @return ProductosImagenes[] Returns an array of ProductosImagenes objects
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
    public function findOneBySomeField($value): ?ProductosImagenes
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
