<?php

namespace Bluehouseapp\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\JsonResponse;

class SecurityController extends Controller
{

    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);

        } elseif (null !== $session && $session->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);

        } else {
            $error = '';

        }

        if ($error) {
            $error = $error->getMessage(); // WARNING! Symfony source code identifies this line as a potential security threat.

        }

        $lastUsername = (null === $session) ? '' : $session->get(SecurityContext::LAST_USERNAME);

        return $this->render(
            'BluehouseappWebBundle:Frontend:login.html.twig',
            array(
                'last_username' => $lastUsername,
                'error' => $error,

            )

        );

    }

    public function loginCheckAction(Request $request)
    {


    }

    public function userAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if ($user) {
            return new JsonResponse(array(
                'id' => $user->getId(),
                'username' => $user->getUsername()
            ));
        }

        return new JsonResponse(array(
            'message' => 'User is not identified'
        ));

    }

} 