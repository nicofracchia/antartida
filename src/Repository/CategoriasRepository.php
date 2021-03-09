<?php

namespace App\Repository;

use App\Entity\Categorias;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Categorias|null find($id, $lockMode = null, $lockVersion = null)
 * @method Categorias|null findOneBy(array $criteria, array $orderBy = null)
 * @method Categorias[]    findAll()
 * @method Categorias[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoriasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categorias::class);
    }
    
    public function eliminarArbol($id){
        $conn = $this->getEntityManager()->getConnection();

        $SQL  = "UPDATE categorias SET eliminado = 1 WHERE id = ".$id." OR grupo LIKE '%".$id."%'";

        $stmt = $conn->prepare($SQL);
        $stmt->execute();
        
        return $id;
    }
}
