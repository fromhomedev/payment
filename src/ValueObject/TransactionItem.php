<?php

declare(strict_types=1);

namespace FromHome\Payment\ValueObject;

use Psl\Type;

final class TransactionItem
{
    private string $id;

    private float $price;

    private int $quantity;

    private string $name;

    private ?string $brand;

    private ?string $category;

    private ?string $merchantName;

    private bool $isService;

    private array $input;

    /**
     * @param array{
     *  id: string,
     *  price: float,
     *  quantity: int,
     *  name: string,
     *  brand?: string,
     *  category?: string,
     *  merchantName?: string,
     *  isService?: boolean
     * } $input
     */
    public function __construct(array $input)
    {
        $input = Type\shape([
            'id' => Type\non_empty_string(),
            'price' => Type\float(),
            'quantity' => Type\int(),
            'name' => Type\non_empty_string(),
            'brand' => Type\nullable(Type\non_empty_string()),
            'category' => Type\nullable(Type\non_empty_string()),
            'merchantName' => Type\nullable(Type\non_empty_string()),
            'isService' => Type\nullable(Type\bool()),
        ])->coerce($input);

        $this->id = $input['id'];
        $this->price = $input['price'];
        $this->quantity = $input['quantity'];
        $this->name = $input['name'];
        $this->brand = $input['brand'];
        $this->category = $input['category'];
        $this->merchantName = $input['merchantName'];
        $this->isService = $input['isService'] ?? false;

        $this->input = $input;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function getMerchantName(): ?string
    {
        return $this->merchantName;
    }

    public function isService(): bool
    {
        return $this->isService;
    }

    public function getInput(): array
    {
        return $this->input;
    }
}
