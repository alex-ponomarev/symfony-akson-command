<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    private LoggerInterface $logger;
    public function __construct(ManagerRegistry $registry,LoggerInterface $logger)
    {
        $this->logger=$logger;
        parent::__construct($registry, Product::class);
    }
    public function findByCategoryField($id)
    {
            try {
                return $this->createQueryBuilder('p')
                    ->andWhere('p.category = :val')
                    ->setParameter('val', $id)
                    ->getQuery()
                    ->getResult();
            }
        catch (Exception $err){
                $this->logger->error($err->getMessage());

       }
    }
    public function findOneBySomeField($id)
    {
        try {
            return $this->createQueryBuilder('p')
                ->andWhere('p.category = :val')
                ->setParameter('val', $id)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (Exception $err) {
            $this->logger->error($err->getMessage());

        }
    }


    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
/*
    public function findByExampleField($id)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :val')
            ->setParameter('val', $id)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
*/

    /*
    public function findOneBySomeField($value): ?Product
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
