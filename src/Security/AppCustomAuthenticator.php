<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class AppCustomAuthenticator extends AbstractAuthenticator
{
    public function supports(Request $request): ?bool
    {
        return str_starts_with($request->headers->get('Authorization'),'Bearer ');
    }

    public function authenticate(Request $request): Passport
    {
        $apiKey = str_replace('Bearer ', '', $request->headers->get('Authorization'));
        return new Passport(new UserBadge($apiKey), new CustomCredentials(function ($credentials, UserInterface $user) {
          /** @var User $user */
          return $user->getApiKey() === $credentials;
        },$apiKey));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
      return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
      return new JsonResponse(['error' => "Erreur d'authentification"],Response::HTTP_FORBIDDEN);
    }
}
