<?php

namespace App\Tests\Controller;

use App\Factory\ProductFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class ProductControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testIndex(): void
    {
        $client = static::createClient();

        //Créer un produit dont on connaît le nom
        $product = ProductFactory::createOne(['name' => 'Test Product XYZ']);

        $client->request('GET', '/product');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('body', 'Test Product XYZ');
    }

    public function testShow(): void
    {
        $client = static::createClient();

        $product = ProductFactory::createOne(['name' => 'Detail Product ABC']);

        //Récupérer le slug du produit créé
        $slug = $product->getSlug();

        $client->request('GET', '/product/' . $slug);

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Detail Product ABC');
    }



}
