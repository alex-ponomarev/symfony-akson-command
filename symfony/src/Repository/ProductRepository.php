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
        $categoryNumber = null;
        $product = new Product();
        $product->setName($fields['name']);
        if(isset($fields['sku']))
        $product->setSku($fields['sku']);
        if(isset($fields['price']))
        $product->setPrice($fields['price']);
        if(isset($fields['description']))
        $product->setDescription($fields['description']);
        if(isset($fields['category'])) {
            $product->setCategory($fields['category']);
            $categoryNumber = $fields['category'];
        }
        $this->_em->persist($product);
        $this->_em->flush();
        return ['Новый продукт внесен в БД и существует под id = '.$product->getId(), 200, $categoryNumber];
    }

    public function patch($id,$fields)
    {
        $product = $this->findBy(array('id' => $id));
        $product->setName($fields['name']);
        if(isset($fields['category']))
            $product->setCategory($fields['category']);
        if(isset($fields['sku']))
            $product->setSku($fields['sku']);
        if(isset($fields['price']))
            $product->setPrice($fields['price']);
        if(isset($fields['description']))
            $product->setDescription($fields['description']);
        $this->_em->flush();
        return ['Продукт '.$id.' успешно обновлен', 200];
    }

    public function delete($id)
    {
        $product = $this->findOneBy(array('id' => $id));
        if($product !== null) {
            $categoryNumber = $product->getCategory();
            $this->_em->remove($product);
            $this->_em->flush();
            return ['Продукт ' . $id . ' был удален', 200, $categoryNumber];
        }
        else
        {
            return ['Продукта с id = ' . $id . ' не существует', 418, null];
        }
    }
    public function productsRemountToSingularity($id)
    {
        $products = $this->findBy(array('category' => $id));
        foreach ($products as $product){
            $product->setCategory(0);
        }
        $this->_em->flush();
        return ['Продукты с категорией '.$id.' были отправлены в Сингулярность', 200];
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
