<?php


namespace App\EventListener;
use App\Entity\Category;
use App\Entity\Product;
use App\Security\Authorization;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\PreRemove;
use Symfony\Component\HttpClient\CurlHttpClient;

class ProductChangedNotifier
{
    private string $token;
    /**
     * @var Authorization
     */
    private Authorization $authorization;

    public function __construct()
    {
        $this->authorization = new Authorization();
        $this->token = $this->authorization->loginToProductService($_ENV['URL_SERVICE_PRODUCT']);
    }
         /** @PreRemove */
        public function preDelete(Product $product, LifecycleEventArgs $event)
        {
            $entity = $event->getObject();
            $entityManager = $event->getObjectManager();

            if ($entity instanceof Category) {
                $client = new CurlHttpClient();
               try {
                   $client->request(
                       'GET',
                       $this->getParameter('category_service_url').'/api/category/count_decrease/' . $product->getId(), [
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