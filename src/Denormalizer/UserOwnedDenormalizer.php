<?php
namespace App\Denormalizer;

use App\Entity\User;
use App\Interface\UserOwnedInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

class UserOwnedDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    const ALREADY_CALLED = 'ALREADY_CALLED';

    public function __construct(protected Security $security){}

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        $reflection = new \ReflectionClass($type);
        $alreadyCalled = $data[self::ALREADY_CALLED] ?? false;
        return ($reflection->implementsInterface(UserOwnedInterface::class) && !$alreadyCalled);
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
      $data[self::ALREADY_CALLED] = true;
      $obj = $this->denormalizer->denormalize($data, $type, $format, $context);
      /** @var User $user */
      $user = $this->security->getUser();
      if ($user)
      {
        $obj->setUser($user);
      }
//      dd($obj);
      return $obj;
    }
}
