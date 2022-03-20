<?php
namespace App\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\User;
use App\Interface\UserOwnedInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

class UserOwnedExtension implements QueryItemExtensionInterface, QueryCollectionExtensionInterface
{
  public function __construct(protected Security $security){}

  public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = [])
  {
    $this->conditionUserOwned($resourceClass, $queryBuilder);
  }

  public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
  {
    $this->conditionUserOwned($resourceClass, $queryBuilder);
  }

  private function conditionUserOwned($resourceClass, $queryBuilder)
  {
    /** @var User $user */
    $user = $this->security->getUser();
    if ($user) {
      $reflection = new \ReflectionClass($resourceClass);
      if ($reflection->implementsInterface(UserOwnedInterface::class)) {
        $alias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere($alias.'.user = :user_id');
        $queryBuilder->setParameter(':user_id', $user->getId());
      }
    }
  }
}
