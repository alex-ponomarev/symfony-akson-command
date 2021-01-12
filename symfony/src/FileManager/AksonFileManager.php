<?php

namespace App\FileManager;

use Doctrine\DBAL\Driver\Exception;
use Elasticsearch\ClientBuilder;
use Symfony\Component\Finder\Finder;
use App\DataCrypt\Encoder;
use Symfony\Component\HttpClient\CurlHttpClient;
use App\Repository\ProductRepository;

class AksonFileManager
{
    private \Elasticsearch\Client $elastic;

    public function __construct(ProductRepository $repository){
        $hosts = ['172.31.0.1:9200'];
        $this->elastic = ClientBuilder::create()
            ->setHosts($hosts)
            ->setRetries(count($hosts))
            ->build();
        $this->repository = $repository;
    }
    public function startProcessFile()
    {
        $productFields = $this->explodeFile($this->findAksonFile());
        // TODO: проверить состояние доступности БД
        $this->repository->fileProductsPost($productFields);
        // TODO: проверить состояние доступности Elastic
        $this->elasticAdd($productFields);
    }
    function findAksonFile()
    {
        $finder = new Finder();
        $finder->files()->in('/var/www/symfony/public/resource');

        foreach ($finder as $file) {
            return $file;
        }
    }
    private function explodeFile($file){
        $productsElements = explode("|",strval($file->getContents()));
        $productFields = [];
        for($i = 0; $i+1<count($productsElements);$i+=6){
            $product = [];
            if($i!=0){
            $concatenate = explode("\n",$productsElements[$i]);
                $product['sku'] = $concatenate[1];
            }else {
                $product['sku'] = $productsElements[$i];
            }
            $product['name'] = $productsElements[$i+1];
            $product['price'] = $productsElements[$i+2];
            $product['description'] = $productsElements[$i+3];
            array_push($productFields,$product);
        }
        return $productFields;
    }
    private function elasticAdd($productFields){
        $i = 0;
        foreach($productFields as $product) {
            $params = [
                'index' => 'product',
                'id' => '0',
                'body' => [
                    'sku' => $product['sku'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'description' => $product['description']]
            ];
            $response = $this->elastic->index($params);
            //dump($response);
            $i++;
        }
    }

    /* private function elasticAdd($productFields){
        $client = new CurlHttpClient();
        $i = 0;
        foreach($productFields as $product) {
            try {
                $response = $client->request(
                    'POST',
                    'http://'.'172.31.0.1'.':9200/product/'.$i, [
                        'json' => [
                            'sku' => $product['sku'],
                            'name' => $product['name'],
                            'price' => $product['price'],
                            'description' => $product['description']],
                        'headers' => [
                            'Content-Type' => 'application/json',]
                    ]
                );
                if ($i == 0) {
                    dump($response->getStatusCode());
                    dump($response->getInfo());
                    dump($response->getContent());
                }
                $i++;
            }
            catch (Exception $err){
                dump($err->getMessage());
            }
        }
    }
   */
}