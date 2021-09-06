<?php

declare(strict_types=1);

namespace FromHome\Payment\ValueObject;

use Psl\Type;

final class Transaction
{
    private string | int $id;

    private float $amount;

    private string $currency;

    private ?Customer $customer;

    private ?Customer $billingAddress;

    private ?Customer $shippingAddress;

    private array $items = [];

    private array $input;

    /**
     * @param array{
     *  id: string|int,
     *  amount: float,
     *  currency?: string,
     *  customer?: Customer,
     *  billingAddress?: Customer,
     *  shippingAddress?: Customer,
     *  items?: TransactionItem[]
     * } $input
     */
    public function __construct(array $input)
    {
        $input = Type\shape([
            'id' => Type\union(Type\non_empty_string(), Type\int()),
            'amount' => Type\float(),
            'currency' => Type\optional(Type\union(Type\null(), Type\non_empty_string())),
            'customer' => Type\optional(Type\union(Type\null(), Type\object(Customer::class))),
            'billingAddress' => Type\optional(Type\union(Type\null(), Type\object(Customer::class))),
            'shippingAddress' => Type\optional(Type\union(Type\null(), Type\object(Customer::class))),
            'items' => Type\optional(Type\dict(Type\array_key(), Type\object(TransactionItem::class))),
        ])->coerce($input);

        $this->id = $input['id'];
        $this->amount = $input['amount'];
        $this->currency = $input['currency'] ?? 'IDR';
        $this->customer = $input['customer'] ?? null;
        $this->billingAddress = $input['billingAddress'] ?? null;
        $this->shippingAddress = $input['shippingAddress'] ?? null;
        $this->items = $input['items'] ?? [];

        $this->input = $input;
    }

    public function getId(): string | int
    {
        return $this->id;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function getBillingAddress(): ?Customer
    {
        return $this->billingAddress;
    }

    public function getShippingAddress(): ?Customer
    {
        return $this->shippingAddress;
    }

    /**
     * @return TransactionItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getInput(): array
    {
        return $this->input;
    }
}
