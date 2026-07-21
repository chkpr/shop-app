<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Address;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class OrderService
{
    public function __construct(
        private CartService $cartService,
        private EntityManagerInterface $entityManager,
    ){
    }

    public function createOrderFromCart(User $customer, Address $shippingAddress): Order
    {
        $order = new Order();
        $order->setCustomer($customer);
        $order->setShippingAddress($shippingAddress);
        $order->setCreatedAt(new \DateTimeImmutable());
        $order->setStatus('pending');

        $total=0;

        foreach ($this->cartService->getFullCart() as $item) {
            $orderItem = new OrderItem();
            $orderItem->setProduct($item['product']);
            $orderItem->setQuantity($item['quantity']);
            $orderItem->setPrice($item['product']->getPrice());
            $orderItem->setPurchaseOrder($order);

            $order->addItem($orderItem);

            $total += $item['product']->getPrice() * $item['quantity'];
        }

        $order->setTotal($total);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $this->cartService->clear();

        return $order;



    }

}
