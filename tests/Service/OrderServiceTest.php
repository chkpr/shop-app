<?php

declare(strict_types=1);

namespace App\Tests\Services;

use App\Factory\ProductFactory;
use App\Factory\UserFactory;
use App\Factory\AddressFactory;
use App\Service\OrderService;
use App\Service\CartService;
use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class OrderServiceTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    private CartService $cartService;
    private OrderService $orderService;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $container = static::getContainer();

        $request = new Request();
        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);
        $container->get('request_stack')->push($request);

        $this->cartService = $container->get(CartService::class);
        $this->orderService = $container->get(OrderService::class);
    }
    public function testOrderPriceIsFrozenAtPurchaseTime(): void
    {
        $product = ProductFactory::createOne(['price' => 1000]);
        $this->cartService->add($product->getId());

        $user = UserFactory::createOne(['email' => 'test@test.com']);
        $address = AddressFactory::createOne(['street' => 'Test Street', 'city' => 'Test City']);


        $order = $this->orderService->createOrderFromCart($user, $address);

        $product->setPrice(2000);

        $firstItem = $order->getItems()->first();
        self::assertSame(1000, $firstItem->getPrice());
    }
}

