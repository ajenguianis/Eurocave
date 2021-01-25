<?php

namespace App\Controller\Admin;

use App\Entity\Code;
use App\Entity\Group;
use App\Entity\Tracking;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin_eurocave")
     * @return Response
     */
    public function index(): Response
    {
        $routeBuilder = $this->get(CrudUrlGenerator::class)->build();

        return $this->redirect($routeBuilder->setController(UserCrudController::class)->generateUrl());
    }
    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Users Management');
        yield MenuItem::linkToCrud('Users', 'fa fa-users', User::class);
        yield MenuItem::linkToCrud('Groups', 'fa fa-user-circle-o', Group::class);
        yield MenuItem::linkToCrud('Tracking', 'fa fa-sign-in', Tracking::class);
        yield MenuItem::section('Serial number Management');
        yield MenuItem::linkToCrud('Import', 'fa fa-key', Code::class);
    }
    public function configureCrud(): Crud
    {
        return Crud::new()
            ->overrideTemplate('crud/index', 'admin/pages/index.html.twig')
            ;
    }
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            // the name visible to end users
            ->setTitle('Eurocave')
            ;
    }

}