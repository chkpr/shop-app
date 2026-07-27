<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Order;
use App\Factory\UserFactory;
use App\Factory\AddressFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class OrderAccessTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testUserCannotViewAnotherUsersOrder(): void
    {
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine')->getManager();

        // deux utilisateurs distincts
        $owner = UserFactory::createOne(['email' => 'owner@test.com']);
        $intruder = UserFactory::createOne(['email' => 'intruder@test.com']);
        $address = AddressFactory::createOne();

        // une commande appartenant à owner
        $order = new Order();
        $order->setCustomer($owner);
        $order->setShippingAddress($address);
        $order->setStatus('paid');
        $order->setTotal(1000);
        $order->setCreatedAt(new \DateTimeImmutable());
        $em->persist($order);
        $em->flush();

        // l'intrus se connecte et tente de voir la commande de owner
        $client->loginUser($intruder);
        $client->request('GET', '/order/' . $order->getId() . '/confirmation');

        self::assertResponseStatusCodeSame(403);
    }

    public function testUserCanViewOwnOrder(): void
    {
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine')->getManager();

        $owner = UserFactory::createOne(['email' => 'owner2@test.com']);
        $address = AddressFactory::createOne();

        $order = new Order();
        $order->setCustomer($owner);
        $order->setShippingAddress($address);
        $order->setStatus('paid');
        $order->setTotal(1000);
        $order->setCreatedAt(new \DateTimeImmutable());
        $em->persist($order);
        $em->flush();

        $client->loginUser($owner);
        $client->request('GET', '/order/' . $order->getId() . '/confirmation');

        self::assertResponseIsSuccessful();
    }
}
