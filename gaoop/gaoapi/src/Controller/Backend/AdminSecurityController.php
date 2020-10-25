<?php

namespace App\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminSecurityController extends AbstractController
{
    /**
     * User: gao
     * Date: 2020/3/20
     * Description: 登录
     * @param AuthenticationUtils $authenticationUtils
     * @Route("/login", name="app_login")
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('sonata_admin_dashboard');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('Backend/security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * User: gao
     * Date: 2020/3/20
     * Description: 登出
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
