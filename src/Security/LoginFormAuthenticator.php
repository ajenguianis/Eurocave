<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\GroupRepository;
use App\Repository\TrackingRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use App\Services\EncryptService;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private $entityManager;
    private $urlGenerator;
    private $csrfTokenManager;
    private $passwordEncoder;
    private $trackingRepository;
    private $userRepository;
    private $groupRepository;
    private $translator;

    public function __construct(TranslatorInterface $translator, GroupRepository $groupRepository,UserRepository $userRepository,TrackingRepository  $trackingRepository,EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->trackingRepository=$trackingRepository;
        $this->userRepository=$userRepository;
        $this->groupRepository=$groupRepository;
        $this->translator=$translator;
    }

    public function supports(Request $request)
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email'], 'isActive'=>1]);

        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException($this->translator->trans('User could not be found.'));
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
//        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
//            return new RedirectResponse($targetPath);
//        }
        $user = $token->getUser();

        if($request->request->get('serial') && !empty($request->request->get('serial'))){
            $user->setSerialNumber($request->request->get('password'));
            $this->userRepository->save($user);
        }

        /*add tracking*/
        $this->trackingRepository->createTracking($user);

        /*set cache*/

        $cache = new FilesystemAdapter();
        $userID=$cache->getItem('user_id');
        $userID->set($user->getId());
        $cache->save($userID);
        if(in_array('ROLE_ADMIN', $user->getRoles())){
            $request->getSession()->set('_locale', 'fr');
            return new RedirectResponse($this->urlGenerator->generate('admin_eurocave'));
        }

       return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }

    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
