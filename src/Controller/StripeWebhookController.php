<?php

declare(strict_types=1);

namespace App\Controller;


use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Psr\Log\LoggerInterface;
use App\Service\OrderMailer;
final class StripeWebhookController extends AbstractController
{


    public function __construct(
        #[Autowire('%stripe_webhook_secret%')] private string $webhookSecret,
        private LoggerInterface $logger,
        private OrderMailer $mailer,
    ) {
    }

    #[Route('/stripe/webhook', name: 'stripe-webhook', methods: ['POST'])]
    public function handle(
        Request $request,
        OrderRepository $orderRepository,
        EntityManagerInterface $entityManager,
    ): Response {
        $payload = $request->getContent();
        $signature = $request->headers->get('stripe-signature');

        try {
            $event = Webhook::constructEvent($payload, $signature, $this->webhookSecret);
        } catch (SignatureVerificationException $e) {
            return new Response('Signature invalide', 400);
        }

        $this->logger->info('Webhook Stripe reçu', ['type' => $event->type]);

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $orderId = $session->metadata->order_id ?? null;



            if($orderId) {
                $order = $orderRepository->find($orderId);

                if($order){
                    $order->setStatus('paid');
                    $entityManager->flush();

                    $this->mailer->sendOrderConfirmation($order);

                    $this->logger->info('Paiement reçu, commande envoyée', ['orderId' => $order->getId()]);
                }
            }
        }

        return new Response('OK', 200);
    }

}

