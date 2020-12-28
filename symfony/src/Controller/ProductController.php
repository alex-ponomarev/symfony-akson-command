<?php

namespace App\Controller;

use App\DataCrypt\Encoder;
use App\Repository\CategoryRepository;
use App\Security\Authorization;
use App\Validator\CategoryValidator;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Entity\Category;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProductController extends AbstractController
{
    private $client;
    private $token;
    /**
     * @var ProductValidator
     */
    private ProductValidator $validator;
    /**
     * @var Encoder
     */
    private Encoder $encoder;
    /**
     * @var ProductRepository
     */
    private ProductRepository $repository;
    /**
     * @var Authorization
     */
    private Authorization $authorization;

    public function __construct(HttpClientInterface $client,
                                ProductRepository $repository,
                                ProductValidator $validator,
                                Encoder $encoder,
                                Authorization $authorization)
    {
        $this->client = $client;
        $this ->validator = $validator;
        $this ->encoder = $encoder;
        $this->repository = $repository;
        $this->authorization = $authorization;
        $this->token =  $this->authorization->loginToProductService();

    }
    /**
     * @Route("/api/category/login_get_token/{username}&{password}",
     *     name="getToken",
     *     methods={"GET"})
     * @OA\Get(
     *     summary="Авторизоваться и получить токен",
     *     tags={"Login"})
     * @param Request $request
     * @return Response
     */
    public function getToken(Request $request): Response
    {
        $password = $request->get('password');
        $username = $request->get('username');
        $response = $this->client->request(
            'POST',
            'http://10.44.0.230:9191/api/category/login_check',[
                'json' =>['username'=>$username,'password'=>$password],
                'headers' => [
                    'Content-Type' => 'application/json',]
            ]
        );
        return new Response($response->getContent('token'));
    }
    /**
     * @Route("/api/product",
     *     name="getAll",
     *     methods={"GET"})
     * @OA\Get(
     *     summary="Получить все категории в таблице",
     *     tags={"Basic"})
     */
    public function getAll(): Response
    {

        try {
            $categoryFields = $this->repository->findAll();
            return new Response($this->encoder->toJSON($categoryFields));
        }
        catch (Exception $err){
            return new Response($err->getMessage(),418);
        }
    }

    /**
     * @Route("/api/product/{id}",name="getByID",methods={"GET"})
     * @param Request $request
     * @return Response
     * @OA\Get(
     *     summary="Получить категорию по ID",
     *     tags={"Basic"})
     */
    public function productGetByID(Request $request): Response
    {
        try {
            $id = $request->get('id');
            $this->validator->idValidation($id);
            $fields = $this->repository->findOneBy(array('id' => $id));
            return new Response($this->encoder->toJSON($fields));
        } catch (Exception $err) {
            return new Response($err->getMessage(),418);
        }
    }
    /**
     * @Route("/product/category/{id}",name="productInCategoryGetByID",methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function productInCategoryGetByID(Request $request): Response
    {
        $id = $request->get('id');
        $this->validator->idValidation($id);
        $products = $this->repository->findByCategoryField($id);
        return new Response($this->toJSON($products));
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
