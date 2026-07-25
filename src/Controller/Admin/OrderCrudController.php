<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'N°')->onlyOnIndex(),
            AssociationField::new('customer', 'Client'),
            DateTimeField::new('createdAt', 'Date')->setFormTypeOption('disabled', true),
            MoneyField::new('total', 'Total')->setCurrency('EUR')->setStoredAsCents(true)
                ->setFormTypeOption('disabled', true),
            ChoiceField::new('status', 'Status')
                ->setChoices([
                    'En attente' => 'pending',
                    'Payée' => 'paid',
                    'Expédiée' => 'shipped',
                    'Livrée' => 'delivered'
                ]),
        ];
    }


    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::DELETE)
            ->add(Action::INDEX, Action::DETAIL);
    }
}
