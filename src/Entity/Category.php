<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Interface\UserOwnedInterface;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ApiResource(
  collectionOperations: [
    'get' => ['normalization_context' => ['groups' => ['Category:collection:get']]],
    'post' => ['denormalization_context' => ['groups' => ['Category:collection:post']]]
  ],
  itemOperations: [
    'get' => ['normalization_context' => ['groups' => ['Category:collection:get','Category:item:get']]],
    'put' => ['denormalization_context' => ['groups' => ['Category:collection:post']]],
    'delete'
  ],
  paginationEnabled: false
)]
#[ApiFilter(SearchFilter::class, properties: ['name' => SearchFilterInterface::STRATEGY_PARTIAL])]
#[ORM\HasLifecycleCallbacks]
class Category implements UserOwnedInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
      'Category:collection:get',
      'Product:item:get'
    ])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups([
      'Category:collection:get',
      'Category:collection:post',
      'Product:item:get'
    ])]
    #[Assert\NotBlank(message: "Le nom ne doit pas être vide")]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups([
      'Category:collection:get',
      'Category:collection:post',
      'Product:item:get'
    ])]
    #[Assert\NotBlank(message: "Le slug ne doit pas être vide")]
    private $slug;

  #[ORM\Column(type: 'datetime')]
  #[Groups([
      'Auth:get',
      'Product:item:get'
    ])]
    private $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups([
      'Auth:get',
      'Product:item:get'
    ])]
    private $updatedAt;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Product::class)]
    #[Groups(['Category:item:get'])]
    private $products;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'categories')]
    private $user;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setCategory($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getCategory() === $this) {
                $product->setCategory(null);
            }
        }

        return $this;
    }

  #[ORM\PrePersist]
  public function onPrePersist()
  {
    $this->createdAt = new \DateTime();
  }

    #[ORM\PreFlush]
    public function onFlush()
    {
      $this->updatedAt = new \DateTime();
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
