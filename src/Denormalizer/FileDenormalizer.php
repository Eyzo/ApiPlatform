<?php
namespace App\Denormalizer;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;

class FileDenormalizer implements ContextAwareDenormalizerInterface
{
  const ALREADY_CALLED = 'ALREADY_CALLED';

  public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
  {
    $alreadyCalled = $context[self::ALREADY_CALLED] ?? false;
    return ($data instanceof File && !$alreadyCalled);
  }

  public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
  {
    $context[self::ALREADY_CALLED] = true;
    return $data;
  }
}
