<?php

namespace App\Service;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    public function __construct(
        private RequestStack      $requestStack,
        private ProductRepository $productRepository,
    )
    {
    }

    public function add(int $id): void
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);


        if (isset($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $session->set('cart', $cart);
    }

    public function decrease(int $id): void
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        if (isset($cart[$id])) {
            if ($cart[$id] > 1) {
                $cart[$id]--;
            } else {
                unset($cart[$id]);
            }
        }
        $session->set('cart', $cart);
    }

    public function remove(int $id): void
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);
        if (isset($cart[$id])) {

            unset($cart[$id]);

        }
        $session->set('cart', $cart);
    }

    public function getFullCart(): array
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        $fullCart = [];

        foreach ($cart as $id => $quantity) {
            $product = $this->productRepository->find($id);

            if ($product) {
                $fullCart[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                ];
            }
        }
        return $fullCart;
    }

    public function getTotal(): int
    {
        $total = 0;

        foreach ($this->getFullCart() as $item) {
            $total += $item['product']->getPrice() * $item['quantity'];
        }
        return $total;
    }
}
