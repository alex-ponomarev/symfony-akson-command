<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ProductController extends AbstractController
{

    /**
     * @Route("/product",
     *     name="productGetAll",
     *     methods={"GET"})
     */
    public function productGetAll(): Response
    {
        $productFields = $this->getDoctrine()->getRepository(Product::class)->findAll();
        return new Response($this->toJSON($productFields));
    }

    /**
     * @Route("/product/{id}",name="productGetByID",methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function productGetByID(Request $request): Response
    {
        $category = new Category();

        $id = $request->get('id');
        $productFields = $this->getDoctrine()->getRepository(Product::class)->findBy(array('id' => $id));
        return new Response($this->toJSON($productFields));
    }
    /**
     * @Route("/product/category/{id}",name="productInCategoryGetByID",methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function productInCategoryGetByID(Request $request): Response
    {
        $id = $request->get('id');
        $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(array('id' => $id));
        $categoryFields = $category->getProductRelation();
        return new Response($this->toJSON($categoryFields));
    }
    /**
     * @Route("/product",name="productPost",methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function productPost(Request $request): Response
    {
        $data=$request->toArray();
        $entityManager = $this->getDoctrine()->getManager();
        $product = new Product();
        $product->setName($data['name']);
        $product->setSku($data['sku']);
        $product->setPrice($data['price']);
        $product->setDescription($data['description']);
        $product->setCategory($data['category']);
        $entityManager->persist($product);
        $entityManager->flush();

        return new Response(null,200);
    }
    /**
     * @Route("/product",name="productPatch",methods={"PATCH","PUT"})
     * @param Request $request
     * @return Response
     */
    public function productPatch(Request $request): Response
    {
        $data=$request->toArray();
        $entityManager = $this->getDoctrine()->getManager();
        $product = $this->getDoctrine()->getRepository(Product::class)->findBy(array('id' => $data['id']));
        $product->setName($data['name']);
        $product->setSku($data['sku']);
        $product->setPrice($data['price']);
        $product->setDescription($data['description']);
        $product->setCategory($data['category']);
        $entityManager->flush();

        return new Response(null,200);
    }
    /**
     * @Route("/product/{id}",name="productDelete",methods={"DELETE"})
     * @param Request $request
     * @return Response
     */
    public function productDelete(Request $request): Response
    {
        $id = $request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $product = $this->getDoctrine()->getRepository(Product::class)->findBy(array('id' => $id));
        $entityManager->remove($product[0]);
        $entityManager->flush();

        return new Response(null,200);
    }


    public function productsChangeCategory($products,$trash,$_this)
    {
        foreach ($products as $product){
            $product->setCategory($trash);
            $entityManager = $_this->getDoctrine()->getManager();
            $entityManager->flush();
        }
    }

    function toJSON($obj){
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        return $serializer->serialize($obj, 'json',['ignored_attributes' => ['product_relation','categoryParentRelation','productRelation']]);
    }
}
