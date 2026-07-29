<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Service\CartService;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveListener;

#[AsLiveComponent]
final class CartCounter
{
    use DefaultActionTrait;

    public function __construct(
        private CartService $cartService
    ){
    }

    public function getCount(): int
    {
        $count=0;
        foreach ($this->cartService->getFullCart() as $item) {
            $count += $item['quantity'];
        }
        return $count;
    }

    #[LiveListener('cart:updated')]
    public function onCartUpdate(): void
    {

    }
}
