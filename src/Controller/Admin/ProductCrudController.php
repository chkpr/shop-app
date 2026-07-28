<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Vich\UploaderBundle\Form\Type\VichImageType;


class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [

            TextField::new('name', 'Nom'),
            TextField::new('slug', 'Slug')->hideOnIndex(),
            TextareaField::new('description', 'Description')->hideOnIndex(),
            MoneyField::new('price', 'Prix')->setCurrency('EUR')->setStoredAsCents(true),
            IntegerField::new('stock', 'Stock'),
            AssociationField::new('category', 'Catégorie'),
            TextField::new('imageFile', 'Image')
                ->setFormType(VichImageType::class)
                ->setFormTypeOptions([

                    'allow_delete' => true,
                    'download_uri' => false,
                ])
                ->onlyOnForms(),
            ImageField::new('imageName', 'Image')
                ->setBasePath('/images/products')
                ->onlyOnIndex(),

        ];
    }

}
