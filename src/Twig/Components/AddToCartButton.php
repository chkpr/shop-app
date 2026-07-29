<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Service\CartService;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class AddToCartButton
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    #[LiveProp]
    public int $productId;

    public function __construct(
        private CartService $cartService,
    ) {
    }

    #[LiveAction]
    public function add(): void
    {
        $this->cartService->add($this->productId);
        $this->emit('cart:updated');
    }
}
