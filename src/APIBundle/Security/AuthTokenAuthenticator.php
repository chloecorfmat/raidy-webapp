<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace APIBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Http\HttpUtils;

class AuthTokenAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface
{
    /**
     * Token duration validation in seconds -> 7 days (24*3600*7).
     */
    const TOKEN_VALIDITY_DURATION = 604800;

    protected $httpUtils;

    /**
     * AuthTokenAuthenticator constructor.
     *
     * @param httpUtils $httpUtils HTTP utils
     */
    public function __construct(HttpUtils $httpUtils)
    {
        $this->httpUtils = $httpUtils;
    }

    /**
     * Create token.
     *
     * @param Request $request     request
     * @param mixed   $providerKey provider key
     *
     * @return PreAuthenticatedToken|void
     */
    public function createToken(Request $request, $providerKey)
    {
        $targetUrl = '/api/auth-tokens';

        // Si la requête est une création de token, aucune vérification n'est effectuée
        if (('POST' === $request->getMethod() && $this->httpUtils->checkRequestPath($request, $targetUrl)) ||
            ('GET' === $request->getMethod() && preg_match('/^\/api\/public\/.*$/', $request->getPathInfo()))
        ) {
            return;
        }

        $authTokenHeader = $request->headers->get('X-Auth-Token');

        if (!$authTokenHeader) {
            throw new BadCredentialsException('X-Auth-Token header is required');
        }

        return new PreAuthenticatedToken(
            'anon.',
            $authTokenHeader,
            $providerKey
        );
    }

    /**
     * @param TokenInterface        $token        token
     * @param UserProviderInterface $userProvider user provider
     * @param mixed                 $providerKey  provider key
     *
     * @return PreAuthenticatedToken
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        if (!$userProvider instanceof AuthTokenUserProvider) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The user provider must be an instance of AuthTokenUserProvider (%s was given).',
                    \get_class($userProvider)
                )
            );
        }

        $authTokenHeader = $token->getCredentials();
        $authToken = $userProvider->getAuthToken($authTokenHeader);

        if (!$authToken || !$this->isTokenValid($authToken)) {
            throw new BadCredentialsException('Invalid authentication token');
        }

        $user = $authToken->getUser();
        $pre = new PreAuthenticatedToken(
            $user,
            $authTokenHeader,
            $providerKey,
            $user->getRoles()
        );

        $pre->setAuthenticated(true);

        return $pre;
    }

    /**
     * @param TokenInterface $token       token interface
     * @param mixed          $providerKey provider key
     *
     * @return bool
     */
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    /**
     * @param Request                 $request   request
     * @param AuthenticationException $exception exception
     *
     * @return \Symfony\Component\HttpFoundation\Response|void
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        // Si les données d'identification ne sont pas correctes, une exception est levée
        throw $exception;
    }

    /**
     * @param mixed $authToken auth token
     *
     * @return bool
     */
    private function isTokenValid($authToken)
    {
        return (time() - $authToken->getCreatedAt()->getTimestamp()) < self::TOKEN_VALIDITY_DURATION;
    }
}
