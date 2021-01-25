<?php

namespace App\Controller;

use App\Repository\GroupRepository;
use App\Repository\TrackingRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;


class DefaultController extends AbstractController
{

    private $urlGenerator;


    public function __construct(UrlGeneratorInterface $urlGenerator)
    {

        $this->urlGenerator = $urlGenerator;
    }
    /**
     * @Route("/", name="app_home")
     */
    public function index(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        if(!$user){
            return new RedirectResponse($this->urlGenerator->generate('app_login'));
        }
        if(in_array('ROLE_ADMIN', $user->getRoles())){
            return new RedirectResponse($this->urlGenerator->generate('admin_eurocave'));
        }
        return $this->render('views/index.html.twig', ['group_id'=>$user->getGroup()->getId()]);
    }
    /**
     *
     * @Route("/change_locale/{locale}", name="change_locale")
     */
    public function changeLocale(Request $request, string $locale)
    {
        // On stocke la langue dans la session
        $request->getSession()->set('_locale', $locale);

        // On revient sur la page précédente
        return $this->redirect($request->headers->get('referer'));
    }
    /**
     *
     * @Route("/check/login", name="check_login")
     */
    public function checkLoginAjaxAction(Request $request){

        $user = $this->getUser();
        if(!$user){
            return new JsonResponse(['status'=>'fail', 'redirectUrl'=>$this->urlGenerator->generate('app_login')]);
        }
        return new JsonResponse(['status'=>'success']);
    }
    /**
     *
     * @Route("/modules/users/story.html", name="check_login_story")
     */
    public function checkLoginStoryAction(Request $request){

        $user = $this->getUser();
        if(!$user){
            return new RedirectResponse($this->urlGenerator->generate('app_login'));
        }
        return $this->redirectToRoute('check_login_story');
    }

}
