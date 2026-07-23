<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class OrderMailer
{
    public function __construct(
        private MailerInterface $mailer,
    ){
}

public function sendOrderConfirmation(Order $order): void
{
    $email = (new TemplatedEmail())
        ->from('no-reply@shop-app.local')
        ->to($order->getCustomer()->getEmail())
        ->subject('Confirmation de votre commande n°' . $order->getId())
        ->htmlTemplate('emails/order_confirmation.html.twig')
        ->context(['order' => $order]);

    $this->mailer->send($email);

    }
}
