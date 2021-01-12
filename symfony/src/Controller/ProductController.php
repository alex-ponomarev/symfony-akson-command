<?php

namespace App\Controller;

use ApiPlatform\Core\Annotation\ApiResource;
use App\DataCrypt\Encoder;
use App\Security\Authorization;
use App\Validator\ProductValidator;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\FileManager\AksonFileManager;

/**
 * @ApiResource()
 * Class ProductController
 * @package App\Controller
 */
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
    /**
     * @var AksonFileManager
     */
    private AksonFileManager $flmanager;

    public function __construct(HttpClientInterface $client,
                                ProductRepository $repository,
                                ProductValidator $validator,
                                Encoder $encoder,
                                Authorization $authorization,
                                AksonFileManager $flmanager)
    {
        $this->client = $client;
        $this->flmanager = $flmanager;
        $this ->validator = $validator;
        $this ->encoder = $encoder;
        $this->repository = $repository;
        $this->authorization = $authorization;
        $this->token =  $this->authorization->loginToProductService($_ENV['URL_SERVICE_PRODUCT']);

    }

    /**
     * @Route("/api/product/elasticload",
     *     name="elasticload",
     *     methods={"GET"})
     * @OA\Get(
     *     summary="Запуск логики эластика",
     *     tags={"Advanced"})
     */
    public function elasticload()
    {
         $this->flmanager->startProcessFile();
    }

    /**
     * @Route("/api/product/login_get_token/{username}&{password}",
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
            $_ENV['URL_SERVICE_PRODUCT'].'/api/login_check',[
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
     *     summary="Получить продукт по ID",
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
     * @Route("/api/product/category/{id}",name="productInCategoryGetByID",methods={"GET"})
     * @param Request $request
     * @return Response
     * @OA\Get(
     *     summary="Получить продукты по ID категории",
     *     tags={"Basic"})
     */
    public function productInCategoryGetByID(Request $request): Response
    {
        try {
        $id = $request->get('id');
        $this->validator->idValidation($id);
        $products = $this->repository->findByCategoryField($id);
        return new Response($this->encoder->toJSON($products));
        } catch (Exception $err) {
            return new Response($err->getMessage(),418);
        }
    }
    /**
     * @Route("/api/product",name="post",methods={"POST"})
     * @param Request $request
     * @return Response
     * @OA\RequestBody(
     *     description="Returns the rewards of an user",
     *     @OA\JsonContent(
     *        type="Category",
     *        ref=@Model(type=Product::class)
     *     )
     * )
     * @OA\Post(
     *     summary="Добавить новый продукт",
     *     tags={"Basic"})
     */
    public function post(Request $request): Response
    {
        try {
            $fields = json_decode($request->getContent(), true);
            $this->validator->fieldsValidation($fields);
            $result = $this->repository->post($fields);
            $categoryNumber = $result[2];
            if($categoryNumber!==null) {
                $this->client->request(
                    'PUT',
                    $_ENV['URL_SERVICE_CATEGORY'] . '/api/category/count_increase/' . $categoryNumber, [
                        'headers' => [
                            "Authorization" => "Bearer " . $this->token
                        ]]
                );
            }
            return new Response($result[0], $result[1]);
        } catch (Exception $err) {
            return new Response($err->getMessage(),418);
        }

    }
    /**
     * @Route("/api/product/{id}",name="patch",methods={"PUT"})
     * @param Request $request
     * @return Response
     * @OA\RequestBody(
     *     description="Returns the rewards of an user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Product::class))
     *     )
     * )
     * @OA\Put(
     *     summary="Обновить поля Продукта заданного по ID",
     *     tags={"Basic"})
     */
    public function patch(Request $request): Response
    {
        try {
            $id = $request->get('id');
            $this->validator->idValidation($id);
            $fields = json_decode($request->getContent(), true);
            $this->validator->fieldsValidation($fields);
            $result = $this->repository->patch($id,$fields);
            return new Response($result[0], $result[1]);
        } catch (Exception $err) {
            return new Response($err->getMessage(),418);
        }
    }
    /**
     * @Route("/api/product/{id}",name="delete",methods={"DELETE"})
     * @param Request $request
     * @return Response
     * @OA\Delete(
     *     summary="Удаляет продукт по ID",
     *     tags={"Basic"})
     */
    public function delete(Request $request): Response
    {
        try {
            $id = $request->get('id');
            $this->validator->idValidation($id);
            $result = $this->repository->delete($id);
            $categoryNumber = $result[2];
            if($categoryNumber!==null) {
                $this->client->request(
                    'PUT',
                    $_ENV['URL_SERVICE_CATEGORY'] . '/api/category/count_decrease/' . $categoryNumber, [
                        'headers' => [
                            "Authorization" => "Bearer " . $this->token
                        ]]
                );
            }
            return new Response($result[0], $result[1]);
        } catch (Exception $err) {
            return new Response($err->getMessage(),418);
        }
        //уведомить категорию
    }
    /**
     * @Route("/api/product/category/delete/{id}",name="remountToSingularity",methods={"DELETE"})
     * @param Request $request
     * @return Response
     * @OA\Delete(
     *     summary="Перебазирует продукты удалённой категории в служебную категорию 'Сингулярность'",
     *     tags={"Advanced"})
     */
    public function remountToSingularity(Request $request): Response
    {
        try {
            $id = $request->get('id');
            $this->validator->idValidation($id);
            $result = $this->repository->productsRemountToSingularity($id);
            return new Response($result[0], $result[1]);
        } catch (Exception $err) {
            return new Response($err->getMessage(),418);
        }
        //уведомить категорию
    }




}
