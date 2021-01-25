<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 * @package App\Controller
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/login/{group}", name="app_login")
     *
     */
    public function login(AuthenticationUtils $authenticationUtils, $group = null, Request $request): Response
    {

        if ($this->getUser()) {

            return $this->redirectToRoute('app_home', ['group' => $group]);
        }
        $cache = new FilesystemAdapter();

        if (!empty($request->query->get('locale')) || !empty($cache->getItem('_locale')->get())) {
            $locale=!empty($request->query->get('locale')) ? $request->query->get('locale') : (!empty($cache->getItem('_locale')->get()) ? $cache->getItem('_locale')->get() : 'fr');
            $request->getSession()->set('_locale', $locale);
            $cache->delete('_locale');
            return $this->redirectToRoute('app_login');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        if (strpos($lastUsername, 'code-') !== false && !empty($error)) {
            $group = 'user';

            return $this->render('security/login.html.twig', ['error' => $error, 'group' => $group]);
        }

//        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error, 'group'=>$group]);
        return $this->render('security/login.html.twig', ['error' => $error, 'group' => $group]);
    }

    /**
     * @Route("/logout/{group}", name="app_logout")
     */
    public function logout($group = null)
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
