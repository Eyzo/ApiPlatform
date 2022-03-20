<?php
namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;

class OpenApiDecorator implements OpenApiFactoryInterface {

  public function __construct(protected OpenApiFactoryInterface $decorated){}

  public function __invoke(array $context = []): OpenApi
  {
    $openApi = $this->decorated->__invoke($context);
    $securitySchema = $openApi->getComponents()->getSecuritySchemes();
    $securitySchema['bearerAuth'] = new \ArrayObject([
      'description' => "Clef d'api",
      'type' => 'http',
      'scheme' => 'bearer'
    ]);
    return $openApi;
  }
}
