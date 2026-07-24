<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Factory\AddressFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Order;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;


class StripeWebhookControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;
    public function testWebhookRejectsInvalidSignature(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/stripe/webhook',
            [],
            [],
            ['HTTP_STRIPE_SIGNATURE' => 'signature_bidon'],
            '{"type": "checkout.session.completed"}'
        );

        self::assertResponseStatusCodeSame(400);
    }

    public function testValidWebhookMarksOrderAsPaid(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        //Création d'une commande de test avec le statut "pending"
        $user = UserFactory::createOne();
        $address = AddressFactory::createOne();

        $order = new Order();
        $order->setCustomer($user);
        $order->setShippingAddress($address);
        $order->setStatus('pending');
        $order->setTotal(1000);
        $order->setCreatedAt(new \DateTimeImmutable());

        $em = $container->get('doctrine')->getManager();
        $em->persist($order);
        $em->flush();

        $secret = $container->getParameter('stripe_webhook_secret');


        $payload = json_encode([
            'type' => 'checkout.session.completed',
            'data' => ['object' => ['metadata' => ['order_id' => (string) $order->getId()]]],
        ]);

        $timestamp = time();
        $signedPayload = $timestamp . '.' . $payload;
        $signature = hash_hmac('sha256', $signedPayload, $secret);
        $stripeSignature = sprintf('t=%d, v1=%s', $timestamp, $signature);

        $client->request(
            'POST',
            '/stripe/webhook',
            [], [],
            ['HTTP_STRIPE_SIGNATURE' => $stripeSignature],
            $payload
        );

        self::assertResponseIsSuccessful();
        self::assertSame('paid', $order->getStatus());



    }
}
