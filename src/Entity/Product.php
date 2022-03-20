<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\ProductsPublishController;
use App\Interface\UserOwnedInterface;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(
  collectionOperations: [
    'get' => ['normalization_context' => ['groups' => ['Product:collection:get']]],
    'post' => ['denormalization_context'=> ['groups' => ['Product:collection:post']]]
  ],
  itemOperations: [
    'get' => ['normalization_context' => ['groups' => ['Product:collection:get', 'Product:item:get']]],
    'put' => ['denormalization_context'=> ['groups' => ['Product:collection:post']]],
    'delete',
    'publish' => [
      'method' => 'post',
      'path' => '/products/{id}/publish',
      'controller' => ProductsPublishController::class,
      'openapi_context' => [
        'summary' => 'Permet de publier un article',
        'requestBody' => [
          'content' => [
            'application/json' => [
              'schema' => [
                'type' => 'object',
                'properties' => []
              ]
            ]
          ]
        ]
      ]
    ]
  ],
  paginationEnabled: false
)]
#[ApiFilter(SearchFilter::class, properties: ['name' => SearchFilterInterface::STRATEGY_PARTIAL])]
#[ORM\HasLifecycleCallbacks]
class Product implements UserOwnedInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
      'Product:collection:get',
      'Category:item:get'
      ])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups([
      'Product:collection:get',
      'Product:collection:post',
      'Category:item:get'
    ])]
    #[Assert\NotBlank(message: "Le nom ne doit pas être vide")]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups([
      'Product:collection:get',
      'Product:collection:post',
      'Category:item:get'
    ])]
    #[Assert\NotBlank(message: "Le slug ne doit as être vide")]
    private $slug;

    #[ORM\Column(type: 'datetime')]
    #[Groups([
      'Product:collection:get',
      'Category:item:get'
    ])]
    private $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups([
      'Product:collection:get',
      'Category:item:get'
    ])]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
    #[Groups([
      'Product:item:get',
      'Product:collection:post'
      ])]
    private $category;

    #[ORM\Column(type: 'boolean')]
    #[Groups([
      'Product:item:get',
    ])]
    private $publish;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'products')]
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    #[ORM\PrePersist]
    public function onPrePersist()
    {
      $this->publish = false;
      $this->createdAt = new \DateTime();
    }

    #[ORM\PreFlush]
    public function onPreFlush()
    {
      $this->updatedAt = new \DateTime();
    }

    public function getPublish(): ?bool
    {
        return $this->publish;
    }

    public function setPublish(bool $publish): self
    {
        $this->publish = $publish;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
