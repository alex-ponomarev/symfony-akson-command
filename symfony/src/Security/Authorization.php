<?php

namespace App\Security;
use Symfony\Component\HttpClient\CurlHttpClient;


class Authorization
{

    function loginToProductService($serviceURL)
    {
        $client = new CurlHttpClient();
        $response = $client->request(
            'GET',
            $serviceURL.'/api/category/login_check',[
                'json' =>['username'=>'akson','password'=>'akson'],
                'headers' => [
                    'Content-Type' => 'application/json',]
            ]
        );
        $statusCode = $response->getStatusCode();
        if ($response->getStatusCode() == 200) {
            $content = json_decode($response->getContent(), true);
            return $content['token'];
        }

    }
}