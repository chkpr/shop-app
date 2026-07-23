<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Order;
use Stripe\Checkout\Session;
use Stripe\StripeClient;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class PaymentService
{
    private StripeClient $stripe;

    public function __construct(
        #[Autowire('%stripe_secret_key%')] string $stripeSecretKey,
    ){
        $this->stripe = new StripeClient($stripeSecretKey);
    }

    public function createCheckoutSession(Order $order, string $successUrl, string $cancelUrl): Session
    {
        $lineItems = [];

        foreach ($order->getItems() as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item->getProduct()->getName(),
                    ],
                    'unit_amount' => $item->getPrice(),
                ],
                'quantity' => $item->getQuantity(),
            ];
        }

        return $this->stripe->checkout->sessions->create([
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'managed_payments' => [
                'enabled' => false,
            ],
            'metadata' => [
                'order_id' => (string) $order->getId(),
            ],
        ]);
    }
}


