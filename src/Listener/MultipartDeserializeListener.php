<?php
namespace App\Listener;

use ApiPlatform\Core\EventListener\DeserializeListener;
use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class MultipartDeserializeListener
{
  public function __construct(protected DeserializeListener $decorated, protected DenormalizerInterface $denormalizer,protected SerializerContextBuilderInterface $contextBuilder){}

  public function onKernelRequest(RequestEvent $event)
  {
    $request = $event->getRequest();
    if($request->isMethodCacheable() || $request->isMethod('DELETE')) {
      return;
    }
    if ($request->getContentType() == 'form') {
      $this->handleData($request);
    } else {
      $this->decorated->onKernelRequest($event);
    }
  }

  /**
   * @param Request $request
   * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
   */
  private function handleData(Request $request)
  {
    $attributes = RequestAttributesExtractor::extractAttributes($request);
    if (empty($attributes))
    {
      return;
    }
    $populated = $request->attributes->get('data');
    $data = array_merge($request->request->all(),$request->files->all());
    $type = $attributes['resource_class'];
    $format = 'multipart';
    $context = $this->contextBuilder->createFromRequest($request, false, $attributes);
    $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $populated;
    $obj = $this->denormalizer->denormalize($data, $type, $format, $context);
    $request->attributes->set('data', $obj);
  }
}
