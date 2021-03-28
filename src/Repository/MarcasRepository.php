<?php

namespace App\Repository;

use App\Entity\Marcas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Marcas|null find($id, $lockMode = null, $lockVersion = null)
 * @method Marcas|null findOneBy(array $criteria, array $orderBy = null)
 * @method Marcas[]    findAll()
 * @method Marcas[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarcasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Marcas::class);
    }

    public function findMarcaLike($marca){
        $conn = $this->getEntityManager()->getConnection();

        $SQL  = "SELECT * FROM marcas WHERE marca LIKE :marca LIMIT 0, 1";

        $params['marca'] = '%'.$marca.'%';

        $STMT = $conn->prepare($SQL);
        
        $STMT->execute($params);

        return $STMT->fetchAssociative();
    }
}
