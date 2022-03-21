<?php
namespace App\Controller;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\Request;

class ProductImgController {
  /**
   * @param Product $data
   * @param Request $request
   */
  public function __invoke($data, Request $request)
  {
    return $data->setImgFile($request->files->get('img'));
  }
}
