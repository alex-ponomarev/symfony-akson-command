<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//use App\Entity\Product;
//se App\Entity\Category;

class DefaultController extends AbstractController
{
    /**
     * @Route("/index")
     */
    public function index(): Response
    {

        $entityManager = $this->getDoctrine()->getManager();

       $product = new Product();
        $product->setName('Samsung A5');
        $product->setPrice(3000);



       $repository = $this->getDoctrine()->getRepository(Category::class);

        //$category = $repository->find(1);

        //$product->setCategory($category);

         $entityManager->persist($product);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new product with id ');
    }
}
