<?php

namespace App\Controller;

use http\Env\Request;
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
    public function index(Request $request): Response
    {

        $data = json_decode($request->getContent(), true);
        $validator = new Validator();
        $validation = $validator->validate(
            $data,
            (object)[
                "type" => "array",
                "properties" => (object)[
                    "new_layout" => (object)[
                        "type"=> "string"
                    ]
                ],
                "required" => [
                    "new_layout"
                ]
            ]);
        if(!$validator->isValid()){
            // json is not valid do something
        }

        return new Response('Index is work right ');
    }
}
