<?php

namespace App\Repository;

use App\Entity\Category;
use App\Validator\CategoryValidator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    /**
     * @var CategoryValidator
     */
    private CategoryValidator $validator;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }
    public function findByCategoryField($id)
    {

            return $this->createQueryBuilder('p')
                ->andWhere('p.category = :val')
                ->setParameter('val', $id)
                ->getQuery()
                ->getResult();

    }
    public function post($fields)
    {

            $category = new Category();
            $category->setName($fields['name']);
            if(isset($fields['category']))
            $category->setCategory($fields['category']);
            $category->setProductCount($fields['productCount']);
            $this->_em->persist($category);
            $this->_em->flush();
            return ['Новая категория внесена и существует под id = '.$category->getId(), 200];


    }
    public function patch($id,$fields)
    {

            $category = $this->findOneBy(array('id' => $id));
            $category->setName($fields['name']);
            if(isset($fields['category']))
            $category->setCategory($fields['category']);
            $category->setProductCount($fields['productCount']);
            $this->_em->flush();
            return ['Категория '.$id.' успешно обновлена', 200];

    }
    public function delete($id)
    {

            $category = $this->findOneBy(array('id' => $id));
            $this->remount($id);
            $this->_em->remove($category);
            $this->_em->flush();
            return ['Категория удалена, id нового родителя дочерней категории(если таковая существовала) = 0', 200];


    }
    function remount($id)
    {

            $children = $this->findByCategoryField($id);
            if (count($children) > 0) {
                foreach ($children as $child) {
                    $child->setCategory(0);
                }
                return true;
            }
                return false;

    }
    function countUpdateIncDcr($id,$method)
    {

            $category = $this->findOneBy(array('id' => $id));
            if($category === null)
                return false;
            if ($method == 'increase')
                $one = 1;
            else if ($method == 'decrease' && $category->getProductCount() > 0)
                $one = -1;
            else return false;
            $category->setProductCount($category->getProductCount() + $one);
            $this->_em->flush();
            return true;

    }
    function countSynchronization($category,$content){

            $category->setProductCount(count($content));
            $this->_em->flush();
            return ['Категории обновлены', 200];

    }
    // /**
    //  * @return Category[] Returns an array of Category objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Category
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    /* public function findOneByCategoryField($value): ?Category
     {
         return $this->createQueryBuilder('c')
             ->andWhere('c.category = :val')
             ->setParameter('val', $value)
             ->getQuery()
             ->getOneOrNullResult()
             ;
     }*/
}