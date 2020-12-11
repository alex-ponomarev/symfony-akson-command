<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validation;

//use App\Entity\Product;
//use App\Entity\Category;

class DefaultController extends AbstractController
{
    protected $categoryJSON;

    /**
     * @Route("/index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        /*   dump('THERE');
           $data = json_decode($request->getContent(), true);
           if($data === null){
               return new Response('JSON not valid');
           }
          dump('json valid');
           $validator = Validation::createValidator();
           $validation = $validator->validate(
               $data,
               (object)[
                   "name" => "string",
                   "category" => "object",
                   "count" => "integer"
               ]);

           if(!$validator->isValid()){
               return new Response('Validator is false');
           }
       */
        return new Response('Index is work right ');
    }

}
/*
$data = json_decode($request->getContent(), true);
        $validator = new Validator();
        $validation = $validator->validate(
            $data,
            (object)[
                "name" => "array",
                "category" => (object)[
                    "new_layout" => (object)[
                        "type"=> "string"
                    ]
                ],
                "required" => [
                    "new_layout"
                ]
            ]);
 */