<?php

namespace AppBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedService implements AccessDeniedHandlerInterface
{
    /**
     * @param Request               $request
     * @param AccessDeniedException $accessDeniedException
     *
     * @return Response|AccessDeniedException
     */
    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        return new AccessDeniedException('Vous n\'êtes pas autorisé à consulter cette page');
    }
}
