<?php


namespace App\EventListener;
use App\Entity\Category;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\PostRemove;
use Doctrine\ORM\Mapping\PreRemove;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CategoryChangedNotifier
{
    private string $token;

    public function __construct()
    {
        $this->token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJuYW1lIjoiZ3VseXV0YXlha292Iiwicm9sZSI6ImFkbWluIiwianRpIjoiNWVlOGJiNWMtZGNmOS00YThmLThkNTEtMDNlYzVmNGM1NjA4IiwiaWF0IjoxNjA3Njg1Mjg1LCJleHAiOjE2MDc2ODg4ODV9.2VkdAPyuJTdMFEH1i7I0b9Uh1-pbn7uq1PLj62TiUpo';
    }
         /** @PreRemove */
        public function preDelete(Category $category, LifecycleEventArgs $event)
        {
            $entity = $event->getObject();
            $entityManager = $event->getObjectManager();

            // perhaps you only want to act on some "Product" entity
            if ($entity instanceof Category) {
                $client = new CurlHttpClient();
               try {
                   $client->request(
                       'GET',
                       'http://10.44.0.229:9191/api/product/delete-cat/' . $category->getId(), [
                           'auth_bearer' => '{"accessToken":"' . $this->token . '"}',
                       ]
                   );
                   return true;
               }
               catch (Exception $err){
                   return false;
               }
               finally{
                   return  false;
               }
            }
            return false;
        }
}