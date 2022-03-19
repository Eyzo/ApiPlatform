<?php
namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Dependency;
use App\Repository\DependencyRepository;

class DependencyDataPersister implements DataPersisterInterface {

  public function __construct(protected DependencyRepository $dependencyRepository){}

  public function supports($data): bool
  {
    return $data instanceof Dependency;
  }

  public function persist($data)
  {
    $this->dependencyRepository->insert($data);
  }

  public function remove($data)
  {
    $this->dependencyRepository->remove($data);
  }
}
