<?php
namespace App\Normalizer;

use App\Interface\UserOwnedInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

class UserOwnedNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
  use NormalizerAwareTrait;

  const ALREADY_CALLED = 'ALREADY_CALLED';

  public function __construct(protected Security $security){}

  public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
  {
    $alreadyCalled = $context[self::ALREADY_CALLED] ?? false;
    if (isset($context['resource_class'])) {
      $reflection = new \ReflectionClass($context['resource_class']);
      return ($reflection->implementsInterface(UserOwnedInterface::class) && !$alreadyCalled);
    }
    return false;
  }

  public function normalize(mixed $object, string $format = null, array $context = [])
  {
    $context[self::ALREADY_CALLED] = true;
    $user = $this->security->getUser();
    if ($user) {
      if (isset($context['groups'])) {
        $context['groups'] = array_merge($context['groups'], ['Auth:get']);
      }  else {
        $context['groups'] = ['Auth:get'];
      }
    }
    return $this->normalizer->normalize($object, $format, $context);
  }
}
