<?php

namespace App\Controller\Admin;

use App\Controller\Admin\ProductCrudController;
use App\Controller\Admin\CategoryCrudController;
use App\Controller\Admin\OrderCrudController;
use App\Controller\Admin\UserCrudController;
use App\Controller\Admin\AddressCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(OrderCrudController::class)->generateUrl());

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // return $this->redirectToRoute('admin_user_index');

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Shop App');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');
        // yield MenuItem::linkTo(SomeCrudController::class, 'The Label', 'fas fa-list');
        yield MenuItem::linkTo(ProductCrudController::class, 'Produits', 'fa fa-box');
        yield MenuItem::linkTo(CategoryCrudController::class, 'Catégories', 'fa fa-tags');
        yield MenuItem::linkTo(OrderCrudController::class, 'Commandes', 'fa fa-shopping-cart');
        yield MenuItem::linkTo(UserCrudController::class, 'Clients', 'fa fa-users');
        yield MenuItem::linkTo(AddressCrudController::class, 'Adresses', 'fa fa-map-marker');

    }
}
