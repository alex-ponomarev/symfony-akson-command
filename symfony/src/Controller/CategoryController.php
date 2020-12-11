<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Entity\Category;
use App\Repository\CategoryRepository;
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
            return new Response('categoryGetByID',418);
        }
        $categoryFields = $this->getDoctrine()->getRepository(Category::class)->findOneBy(array('id' => $id));
        return new Response($this->toJSON($categoryFields));
    }//httpclient
    /**
     * @Route("/category",name="categoryPost",methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function categoryPost(Request $request): Response
    {
        if(!$this->jsonValidation($request)){
            return new Response('categoryPost',418);
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
     * @Route("/category/{id}",name="categoryPatch",methods={"PATCH","PUT"})
     * @param Request $request
     * @return Response
     */
    public function categoryPatch(Request $request): Response
    {
        $id = $request->get('id');
        if(!($this->idValidation($id))){
            return new Response('categoryPatch get not valid id',418);
        }
        if(!$this->jsonValidation($request)){
            return new Response('categoryPatch get not valid json',418);
        }
        $data=$request->toArray();
        $entityManager = $this->getDoctrine()->getManager();
        $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(array('id' => $id));
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
            return new Response('categoryDelete get not valid id',418);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(array('id' => $id));
        $this->categoryRemount($id);

        $entityManager->remove($category);
        $entityManager->flush();
        return new Response('Категория удалена, id нового родителя дочерней категории(если таковая существовала) = 0',200);
    }
    function categoryRemount($id){
        $children = $this->getDoctrine()->getRepository(Category::class)->findByCategoryField($id);
        if(count($children)>0) {
            foreach ($children as $child) {
                $child->setCategory(0);
            }
        }
    }
    /**
     * @Route("/category/count_increase/{id}",name="categoryCountIncrease",methods={"PUT","PATCH"})
     * @param Request $request
     * @return Response
     */
    function categoryCountIncrease (Request $request): Response
    {
        if($this->categoryCountUpdateIncDcr($request,'increase')) {
            return new Response('Категория '.$request->get('id').' обновлена', 200);
        }
        else
        {
            return new Response('Обновление категории не удалось', 418);
        }
    }
    /**
     * @Route("/category/count_decrease/{id}",name="categoryCountDecrease",methods={"PUT","PATCH"})
     * @param Request $request
     * @return Response
     */
    function categoryCountDecrease(Request $request): Response
    {

        if($this->categoryCountUpdateIncDcr($request,'decrease')) {
            return new Response('Категория '.$request->get('id').' обновлена', 200);
        }
        else
        {
            return new Response('Обновление категории не удалось', 418);
        }

    }
    /**
     * @Route("/category/count_update_all/",name="categoryCountUpdateAll",methods={"PUT","PATCH"})
     * @param Request $request
     * @return Response
     */
    function categoryCountUpdateAll(Request $request){
        $categoryFields = $this->getDoctrine()->getRepository(Category::class)->findAll();
        foreach ($categoryFields as $category) {
            $response = $this->client->request(
                'GET',
                'http://10.44.0.229:9191/product/search-cat/'.$category->getId(),[
                'auth_bearer' => '{"accessToken":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJuYW1lIjoiZ3VseXV0YXlha292Iiwicm9sZSI6ImFkbWluIiwianRpIjoiNWVlOGJiNWMtZGNmOS00YThmLThkNTEtMDNlYzVmNGM1NjA4IiwiaWF0IjoxNjA3Njg1Mjg1LCJleHAiOjE2MDc2ODg4ODV9.2VkdAPyuJTdMFEH1i7I0b9Uh1-pbn7uq1PLj62TiUpo"}',
                 ]
            );
            $statusCode = $response->getStatusCode();
            if ($statusCode = $response->getStatusCode() != 200)
                continue;
            $content = json_decode($response->getContent(), true);
            $category->setProductCount(count($content));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        }
        return new Response('Категории обновлены', 200);
    }
    function categoryCountUpdateIncDcr($request,$method){
        $id = $request->get('id');
        if(!($this->idValidation($id))){
            return new Response('categoryCountAdd get not valid id',418);
        }
        $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(array('id' => $id));
        if($method == 'increase') $one = 1; else if($method == 'decrease' && $category->getProductCount()>0) $one=-1; else return false;
        $category->setProductCount($category->getProductCount()+$one);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        return true;
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
         if(!(array_key_exists('name',$data) && preg_match('/^([а-яё\s]+|[a-z\s]+)$/iu',$data['name']))){
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
