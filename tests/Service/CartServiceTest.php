<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Factory\ProductFactory;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class CartServiceTest extends WebTestCase


{
    use Factories;
    use ResetDatabase;

    public function testAddProductToCart(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $request = new Request();
        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);

        $requestStack = $container->get('request_stack');
        $requestStack->push($request);

        $cartService = $container->get(CartService::class);

        //créer un produit dont on connaît le prix
        $product = ProductFactory::createOne(['price' => 1000]);

        //ajouter le produit au panier
        $cartService->add($product->getId());

        //Vérifier que le total est correct (un exemplaire à 1000)
        self::assertSame(1000, $cartService->getTotal());

        //$this->assertSame('test', $kernel->getEnvironment());
        // $routerService = static::getContainer()->get('router');
        // $myCustomService = static::getContainer()->get(CustomService::class);
    }
}
