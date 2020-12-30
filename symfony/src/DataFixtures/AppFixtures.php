<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 20; $i++) {
            $category = new Category();
            if($i==0){
                $category->setName('Singularity');
            }else {
                $category->setName('Категория-' . $i);
            }
            $category->setProductCount(0);
            $category->setCategory($i);
            $manager->persist($category);
        }

        for ($i = 0; $i < 20; $i++) {
            $product = new Product();
            $product->setName('Name-'.$i);
            $product->setSku('TestSku');
            $product->setDescription('TestDescription');
            $product->setPrice(mt_rand(100, 10000));
            $product->setCategory(mt_rand(1, 19));
            $manager->persist($product);
        }

        $user = new User();
        $user->setUsername('akson');
        $user->setPassword('$2y$13$p67.HPPzKe0ODybLNVN0wuXGLdabC527ETJxZ3H.fF6KB60F0ZvQa'); //akson
        $user->setRoles(["ROLE_USER"]);

        $manager->flush();
    }
}
