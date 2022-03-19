<?php
namespace App\Repository;

use App\Entity\Dependency;

class DependencyRepository {

  const FILE_NAME = 'test.json';
  protected string $filePath;

  public function __construct(string $projectDir)
  {
    $this->filePath = $projectDir.DIRECTORY_SEPARATOR.self::FILE_NAME;
  }

  /**
   * @return Dependency[]
   */
  public function findAll(): array
  {
    $tabObject = [];
    foreach ($this->getData() as $name => $version) {
      $dependency = new Dependency();
      $dependency->setName($name)
        ->setVersion($version);
      $tabObject[] = $dependency;
    }
    return $tabObject;
  }

  /**
   * @param $uuid
   * @return Dependency|null
   */
  public function find($uuid): ?Dependency
  {
    foreach ($this->findAll() as $dependency)
    {
      if ($dependency->getUuid() === $uuid) {
        return $dependency;
      }
    }
    return null;
  }

  public function insert(Dependency $dependency)
  {
   $json = $this->getJson();
   $json['require'][$dependency->getName()] = $dependency->getVersion();
   $this->writeData($json);
  }

  public function remove(Dependency $dependency)
  {
    $json = $this->getJson();
    unset($json['require'][$dependency->getName()]);
    $this->writeData($json);
  }

  private function getJson()
  {
    return json_decode(file_get_contents($this->filePath), true);
  }

  private function getData()
  {
    return json_decode(file_get_contents($this->filePath), true)['require'];
  }

  private function writeData($data)
  {
    file_put_contents($this->filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
  }

}
