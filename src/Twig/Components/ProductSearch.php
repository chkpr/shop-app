<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class ProductSearch
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $query = '';

    public function __construct(
        private ProductRepository $productRepository,
    ) {
    }

    /**
     * @return Product[]
     */
    public function getProducts(): array
    {
        if ($this->query === '') {
            return [];
        }

        return $this->productRepository->search($this->query);
    }
}
