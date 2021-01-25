<?php
namespace App\Logout;

use App\Repository\UserRepository;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Contracts\Cache\ItemInterface;

class MyLogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    private $router;
    private $userRepository;

    public function __construct(
        RouterInterface $router,
        UserRepository $userRepository
    ) {
        $this->router = $router;
        $this->userRepository=$userRepository;

    }
    /**
     * {@inheritdoc}
     */
    public function onLogoutSuccess(Request $request)
    {
        $cache = new FilesystemAdapter();
        $userId = $cache->getItem('user_id');

        if(!empty($userId->get())){
            $user=$this->userRepository->findOneBy(['id'=>$userId->get(), 'serialNumber'=>null]);
            if ($user && $user->getGroup()->getId()!==3) {
                return new RedirectResponse($this->router->generate('app_login'));
            }
        }

        if (!empty($request->getSession()->get('_locale'))) {
            $value = $cache->get('_locale', function (ItemInterface $item) use ($request) {
                $item->expiresAfter(3600);

                // ... do some HTTP request or heavy computations
                $computedValue = $request->getSession()->get('_locale');

                return $computedValue;
            });
        }

        $group='user';
        return new RedirectResponse($this->router->generate('app_login', ['group'=>$group]));
    }
}