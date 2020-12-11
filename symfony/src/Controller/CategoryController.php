<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CategoryController extends AbstractController
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @Route("/category",
     *     name="categoryGetAll",
     *     methods={"GET"})
     */
    public function categoryGetAll(): Response
    {
        $categoryFields = $this->getDoctrine()->getRepository(Category::class)->findAll();
        return new Response($this->toJSON($categoryFields));
    }

    /**
     * @Route("/category/{id}",name="categoryGetByID",methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function categoryGetByID(Request $request): Response
    {
        $id = $request->get('id');
        if(!($this->idValidation($id))){
            return new Response(null,400);
        }
        $categoryFields = $this->getDoctrine()->getRepository(Category::class)->findOneBy(array('id' => $id));
        return new Response($this->toJSON($categoryFields));
    }
    /**
     * @Route("/category",name="categoryPost",methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function categoryPost(Request $request): Response
    {
        if(!$this->jsonValidation($request)){
            return new Response(null,400);
        }
        $data=$request->toArray();

        $entityManager = $this->getDoctrine()->getManager();

        $category = new Category();
        $category->setName($data['name']);
        $category->setProductCount($data['count']);
        $category->setCategory($data["category"]);
        $entityManager->persist($category);
        $entityManager->flush();
        return new Response(null, 200);

    }
    /**
     * @Route("/category",name="categoryPatch",methods={"PATCH","PUT"})
     * @param Request $request
     * @return Response
     */
    public function categoryPatch(Request $request): Response
    {
        if(!$this->jsonValidation($request)){
            return new Response(null,400);
        }
        $data=$request->toArray();
        $entityManager = $this->getDoctrine()->getManager();
        $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(array('id' => $data['id']));
        $category->setName($data['name']);
        $category->setCategory($data["category"]);
        $category->setProductCount($data["count"]);
        $entityManager->flush();

        return new Response(null,200);
    }
    /**
     * @Route("/category/{id}",name="categoryDelete",methods={"DELETE"})
     * @param Request $request
     * @return Response
     */
    public function categoryDelete(Request $request): Response
    {
        $id = $request->get('id');
        if(!($this->idValidation($id))){
            return new Response(null,400);
        }
        $product = new ProductController();
        $entityManager = $this->getDoctrine()->getManager();
        $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(array('id' => $id));
        $trash = $this->getDoctrine()->getRepository(Category::class)->findOneBy(array('id' => 0));
        $products = $category->getProductRelation();
        $product->productsChangeCategory($products,$trash,$this);


        $entityManager->remove($category);
        $entityManager->flush();
        return new Response(null,200);
    }

    function productDeleteResponse($json){


    }
    function categoryRemount($id){

    }
    function toJSON($obj): string
    {

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        return $serializer->serialize($obj, 'json',['ignored_attributes' => ['product_relation','productRelation','categoryParentRelation','category']]);
    }
    function jsonValidation($request): bool
    {

        $data = json_decode($request->getContent(), true);
         if($data === NULL){
             return false;
         }
         if(!(array_key_exists('name',$data) && preg_match("/^([а-яё\s]+|[a-z\s]+)$/iu",$data['name']))){
             return false;
         }
         if(!(array_key_exists('count',$data) && is_int($data['count']) && $data['count']>=0)){
             return false;
         }
         if(!(array_key_exists('category',$data) && is_int($data['category']) && $data['category']>=0)){
             return false;
         }
         return true;
    }
    function idValidation($id): bool
    {
        if(!(is_numeric($id) && is_int((int)$id))){
            return false;
        }
        return true;
    }
}
