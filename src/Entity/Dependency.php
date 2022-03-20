<?php
namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
  collectionOperations: [
    'get',
    'post'
  ],
  itemOperations: [
    'get' => [
      'security' => "is_granted('ROLE_USER')",
      'openapi_context' => [
        'security' => [
          [
            'bearerAuth' => []
          ]
        ]
      ]
    ],
    'put' => [
      'security' => "is_granted('ROLE_USER')",
      'openapi_context' => [
        'security' => [
          [
            'bearerAuth' => []
          ]
        ]
      ]
    ],
    'delete' => [
      'security' => "is_granted('ROLE_USER')",
      'openapi_context' => [
        'security' => [
          [
            'bearerAuth' => []
          ]
        ]
      ]
    ]
  ],
  paginationEnabled: false,
)]
class Dependency
{
  #[ApiProperty(description: "Identifiant unique de la ressource", identifier: true)]
  protected string $uuid;

  #[ApiProperty(description: "Nom de la version", default: "Test")]
  #[Assert\NotBlank(message: "Le nom de la version ne peut pas être vide")]
  protected string $name;

  #[ApiProperty(description: "Numéro de la version", default: "5.0.1")]
  #[Assert\NotBlank(message: "Le numéro de la version ne peut pas être vide")]
  protected string $version;

  /**
   * @return string
   */
  public function getUuid(): string
  {
    return $this->uuid;
  }

  /**
   * @param string $uuid
   * @return Dependency
   */
  public function setUuid(string $uuid): Dependency
  {
    $this->uuid = $uuid;
    return $this;
  }

  /**
   * @return string
   */
  public function getName(): string
  {
    return $this->name;
  }

  /**
   * @param string $name
   * @return Dependency
   */
  public function setName(string $name): Dependency
  {
    $this->name = $name;
    $this->uuid = Uuid::uuid5(Uuid::NAMESPACE_DNS, $name);
    return $this;
  }

  /**
   * @return string
   */
  public function getVersion(): string
  {
    return $this->version;
  }

  /**
   * @param string $version
   * @return Dependency
   */
  public function setVersion(string $version): Dependency
  {
    $this->version = $version;
    return $this;
  }

}
