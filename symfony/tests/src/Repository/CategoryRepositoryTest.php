<?php
/*

namespace App\Tests\src\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use PHPUnit\Framework\TestCase;

class CategoryRepositoryTest extends TestCase
{
    public function testGetToken()
    {
       // $calc = new Calculator();
       // $result = $calc->add(30, 12);

        // убедитесь, что ваш калькулятор добавил цифры правильно!
        //$this->assertEquals(42, $result);
    }
    public function testFindByCategoryField($id)
    {

        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getResult();

    }
    public function testPost($fields)
    {

        $category = new Category();
        $category->setName($fields['name']);
        $category->setCategory($fields['category']);
        $category->setProductCount($fields['productCount']);
        $this->_em->persist($category);
        $this->_em->flush();
        return ['Новая категория внесена и существует под id = '.$category->getId(), 200];


    }
    public function testPatch($id,$fields)
    {

        $category = $this->findOneBy(array('id' => $id));
        $category->setName($fields['name']);
        $category->setCategory($fields['category']);
        $category->setProductCount($fields['productCount']);
        $this->_em->flush();
        return ['Категория '.$id.' успешно обновлена', 200];

    }
    public function testDelete($id)
    {

        $category = $this->findOneBy(array('id' => $id));
        $this->remount($id);
        $this->_em->remove($category);
        $this->_em->flush();
        return ['Категория удалена, id нового родителя дочерней категории(если таковая существовала) = 0', 200];


    }
    function testRemount($id)
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
    function testCountUpdateIncDcr($id,$method)
    {

        $category = $this->findOneBy(array('id' => $id));
        if ($method == 'increase')
            $one = 1;
        else if ($method == 'decrease' && $category->getProductCount() > 0)
            $one = -1;
        else return false;
        $category->setProductCount($category->getProductCount() + $one);
        $this->_em->flush();
        return true;

    }
    function testCountSynchronization($category,$content){

        $category->setProductCount(count($content));
        $this->_em->flush();
        return ['Категории обновлены', 200];

    }
}
*/