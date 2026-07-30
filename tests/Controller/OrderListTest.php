<?php


declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Order;
use App\Factory\UserFactory;
use App\Factory\AddressFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class OrderListTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testUserSeesOnlyOwnOrders(): void
    {
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine')->getManager();

        $alice = UserFactory::createOne(['email' => 'alice@test.com']);
        $bob = UserFactory::createOne(['email' => 'bob@test.com']);
        $address = AddressFactory::createOne();

        // une commande pour Alice, une pour Bob
        $aliceOrder = $this->makeOrder($alice, $address, 1000);
        $bobOrder = $this->makeOrder($bob, $address, 2000);
        $em->persist($aliceOrder);
        $em->persist($bobOrder);
        $em->flush();

        // Alice consulte sa liste
        $client->loginUser($alice);
        $client->request('GET', '/mes-commandes');

        self::assertResponseIsSuccessful();
        // sa commande apparaît
        self::assertSelectorTextContains('body', 'Commande n°' . $aliceOrder->getId());
        // celle de Bob n'apparaît pas
        self::assertSelectorTextNotContains('body', 'Commande n°' . $bobOrder->getId());
    }

    private function makeOrder($customer, $address, int $total): Order
    {
        $order = new Order();
        $order->setCustomer($customer);
        $order->setShippingAddress($address);
        $order->setStatus('paid');
        $order->setTotal($total);
        $order->setCreatedAt(new \DateTimeImmutable());

        return $order;
    }
}
