<?php

namespace App\Controller;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Security\Authorization;
use App\Validator\CategoryValidator;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Entity\Category;
use App\DataCrypt\Encoder;
use App\Repository\CategoryRepository;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

/**
 * @ApiResource()
 * Class CategoryController
 * @package App\Controller
 */
class CategoryController extends AbstractController
{

    private $client;
    private string $token;
    /**
     * @var CategoryValidator
     */
    private CategoryValidator $validator;
    /**
     * @var Encoder
     */
    private Encoder $encoder;
    /**
     * @var CategoryRepository
     */
    private CategoryRepository $repository;
    /**
     * @var Authorization
     */
    private Authorization $authorization;

    public function __construct(HttpClientInterface $client,
                                CategoryRepository $repository,
                                CategoryValidator $validator,
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
    //залогиниться и принимать токен, когда на Продукте заработает авторизация
    /**
     * @Route("/api/category/get_token/{username}&{password}",
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
            'GET',
            'http://10.44.0.230:9191/api/category/login_check',[
                'json' =>['username'=>$username,'password'=>$password],
                'headers' => [
                    'Content-Type' => 'application/json',]
                ]
        );
        return new Response($response->getContent('token'));
    }
    /**
     * @Route("/api/category",
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
     * @Route("/api/category/{id}",name="getByID",methods={"GET"})
     * @param Request $request
     * @return Response
     * @OA\Get(
     *     summary="Получить категорию по ID",
     *     tags={"Basic"})
     */
    public function getByID(Request $request): Response
    {
        try {
            $id = $request->get('id');
            $this->validator->idValidation($id);
            $categoryFields = $this->repository->findOneBy(array('id' => $id));
            return new Response($this->encoder->toJSON($categoryFields));
        } catch (Exception $err) {
            return new Response($err->getMessage(),418);
        }
    }
    /**
     * @Route("/api/category",name="post",methods={"POST"})
     * @param Request $request
     * @return Response
     * @OA\RequestBody(
     *     description="Returns the rewards of an user",
     *     @OA\JsonContent(
     *        type="Category",
     *        ref=@Model(type=Category::class)
     *     )
     * )
     * @OA\Post(
     *     summary="Добавить новую категорию",
     *     tags={"Basic"})
     */
    public function post(Request $request): Response
    {
        try {
            $fields = json_decode($request->getContent(), true);
            $this->validator->fieldsValidation($fields);
            $result = $this->repository->post($fields);
            return new Response($result[0], $result[1]);
        } catch (Exception $err) {
            return new Response($err->getMessage(),418);
        }

    }
    /**
     * @Route("/api/category/{id}",name="patch",methods={"PUT"})
     * @param Request $request
     * @return Response
     * @OA\RequestBody(
     *     description="Returns the rewards of an user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Category::class))
     *     )
     * )
     * @OA\Put(
     *     summary="Обновить поля Категории заданной по ID",
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
     * @Route("/api/category/{id}",name="delete",methods={"DELETE"})
     * @param Request $request
     * @return Response
     * @OA\Delete(
     *     summary="Удаляет категорию по ID",
     *     tags={"Basic"})
     */
    public function delete(Request $request): Response
    {
        try {
            $id = $request->get('id');
            $this->validator->idValidation($id);
            $result = $this->repository->delete($id);
            return new Response($result[0], $result[1]);
        } catch (Exception $err) {
            return new Response($err->getMessage(),418);
        }
    }
    /**
     * @Route("/api/category/count_increase/{id}",name="countIncrease",methods={"PUT"})
     * @param Request $request
     * @return Response
     * @OA\Put(
     *     summary="Увеличить количество продуктов в категории(id) на один",
     *     tags={"Advanced"})
     * @OA\Patch (
     *     tags={"Advanced"})
     */
    function countIncrease (Request $request): Response
    {
        try {
            $id = $request->get('id');
            $this->validator->idValidation($id);
            if ($this->repository->countUpdateIncDcr($id, 'increase')) {
                return new Response('Категория ' . $id . ' обновлена', 200);
            } else {
                return new Response('Обновление категории не удалось', 418);
            }
        } catch (Exception $err) {
            return new Response($err->getMessage(),418);
        }
    }
    /**
     * @Route("/api/category/count_decrease/{id}",name="countDecrease",methods={"PUT"})
     * @param Request $request
     * @return Response
     * @OA\Put(
     *     summary="Уменьшить количество продуктов в категории(id) на один",
     *     tags={"Advanced"})
     * @OA\Patch (
     *     tags={"Advanced"})
     */
    function countDecrease(Request $request): Response
    {
        try {
            $id = $request->get('id');
            $this->validator->idValidation($id);
            if ($this->repository->countUpdateIncDcr($id, 'decrease')) {
                return new Response('Категория ' . $id . ' обновлена', 200);
            } else {
                return new Response('Обновление категории не удалось', 418);
            }
        }
        catch (Exception $err){
            return new Response($err->getMessage(),418);
        }
    }
    /**
     * @Route("/api/category/count_synchronization/",name="countSynchronization",methods={"PUT"})
     * @param Request $request
     * @return Response
     * @OA\Put(
     *     summary="Обновить количество продуктов во всех категориях в соответствии с продуктами сервиса Product",
     *     tags={"Advanced"})
     * @OA\Patch (
     *     tags={"Advanced"})
     */
    function countSynchronization(Request $request): Response
    {
        dump($this->token);
        $refreshStatus = ['В данный момент обновление категорий невозможно',418];
        try {
            $categoryFields = $this->getDoctrine()->getRepository(Category::class)->findAll();
            foreach ($categoryFields as $category) {
                //получаем по одной порции, чтобы не убить память
                //когда на сервисе Product будет реализована прямая выдача количества - изменить цикл
                $response = $this->client->request(
                    'GET',
                    'http://10.44.0.229:9191/product/search-cat/' . $category->getId(), [
                        'auth_bearer' => '{"accessToken":"' . $this->token . '"}',
                    ]
                );
                $statusCode = $response->getStatusCode();

                if ($response->getStatusCode() != 200) {
                    $category->setProductCount(0);
                    continue;
                }
                $content = json_decode($response->getContent(), true);
                $this->repository->countSynchronization($category, $content);
                $refreshStatus = ['Категории обновлены',200];
            }
            return new Response($refreshStatus[0], $refreshStatus[1]);
        }
        catch (Exception $err){
            return new Response($err->getMessage(),418);
        }
    }
    /*private function routeToControllerName($routename) {
        $routes = $this->get('router')->getRouteCollection();
        return $routes->get($routename)->getDefaults()['_controller'];
    }*/
}
