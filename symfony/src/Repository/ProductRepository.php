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


    public function post( $fields)
    {
        $product = new Product();
        $product->setName($fields['name']);
        $product->setSku($fields['sku']);
        $product->setPrice($fields['price']);
        $product->setDescription($fields['description']);
        $product->setCategory($fields['category']);
        $this->_em->persist($product);
        $this->_em->flush();

    }

    public function patch($fields)
    {
        $product = $this->findBy(array('id' => $fields['id']));
        $product->setName($fields['name']);
        $product->setSku($fields['sku']);
        $product->setPrice($fields['price']);
        $product->setDescription($fields['description']);
        $product->setCategory($fields['category']);
        $this->_em->flush();

    }

    public function delete($id)
    {
        $product = $this->findBy(array('id' => $id));
        $this->_em->remove($product[0]);
        $this->_em->flush();
        //уведомить категорию
    }

/*
    public function productsRemountCategory($products,$trash,$_this)
    {
        foreach ($products as $product){
            $product->setCategory($trash);
            $entityManager = $_this->getDoctrine()->getManager();
            $entityManager->flush();
        }
    }
*/
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
