<?php

namespace App\Security;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class Authorization
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    function loginToProductService(){
        $response = $this->client->request(
            'GET',
            'http://10.44.0.229:9191/api/login_check',[
                'json' =>['username'=>'akson','password'=>'akson'],
                'headers' => [
                    'Content-Type' => 'application/json',]
            ]
        );//залогиниться и принимать токен, когда яков доделает авторизацию
        $statusCode = $response->getStatusCode();
        if ($response->getStatusCode() == 200) {
            $content = json_decode($response->getContent(), true);
            return $content['token'];
        }

    }
}