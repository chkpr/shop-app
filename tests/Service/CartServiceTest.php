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

    private CartService $cartService;

    protected function setUp(): void
    {

        self::bootKernel();
        $container = static::getContainer();

        $request = new Request();
        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);

        $requestStack = $container->get('request_stack');
        $requestStack->push($request);

        $this->cartService = $container->get(CartService::class);
    }

    public function testAddProductToCart(): void
    {

        //créer un produit dont on connaît le prix
        $product = ProductFactory::createOne(['price' => 1000]);

        //ajouter le produit au panier
        $this->cartService->add($product->getId());

        //Vérifier que le total est correct (un exemplaire à 1000)
        self::assertSame(1000, $this->cartService->getTotal());

        //$this->assertSame('test', $kernel->getEnvironment());
        // $routerService = static::getContainer()->get('router');
        // $myCustomService = static::getContainer()->get(CustomService::class);
    }

    public function testAddTwiceProductToCart(): void
    {
        //Arrange : préparer les données
        $product = ProductFactory::createOne(['price' => 1000]);

        //Act : exécuter l'action testée
        $this->cartService->add($product->getId());
        $this->cartService->add($product->getId());

        //Assert : vérifier le résultat
        self::assertSame(2000, $this->cartService->getTotal());
    }

    public function testEmptyCart(): void
    {
        $product = ProductFactory::createOne(['price' => 1000]);

        $this->cartService->add($product->getId());
        $this->cartService->decrease($product->getId());

        self::assertSame(0, $this->cartService->getTotal());
        self::assertCount(0, $this->cartService->getFullcart());
    }

    public function testAddTwoDifferentProductsToCart(): void
    {
        $product1 = ProductFactory::createOne(['price' => 1000]);
        $product2 = ProductFactory::createOne(['price' => 500]);

        $this->cartService->add($product1->getId());
        $this->cartService->add($product2->getId());

        self::assertSame(1500, $this->cartService->getTotal());
    }
}
