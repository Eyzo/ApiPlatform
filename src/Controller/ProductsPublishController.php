<?php
namespace App\Controller;

use App\Entity\Product;

class ProductsPublishController {
  /**
   * @param Product $data
   */
  public function __invoke($data)
  {
    $data->setPublish(true);
    return $data;
  }
}
