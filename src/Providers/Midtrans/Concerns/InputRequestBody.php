<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Providers\Midtrans\Concerns;

use Webmozart\Assert\Assert;
use Ziswapp\Payment\ValueObject\TransactionItem;

trait InputRequestBody
{
    protected function defaultParams(): array
    {
        Assert::propertyExists($this, 'transaction');

        $default = [
            'transaction_details' => [
                'order_id' => $this->transaction->getId(),
                'gross_amount' => $this->transaction->getAmount(),
            ],
        ];

        if ($this->transaction->getCustomer()) {
            $default['customer_details'] = [
                'first_name' => $this->transaction->getCustomer()->getFirstName(),
                'last_name' => $this->transaction->getCustomer()->getLastName(),
                'email' => $this->transaction->getCustomer()->getEmail(),
                'phone' => $this->transaction->getCustomer()->getPhone(),
                'billing_address' => [
                    'first_name' => $this->transaction->getBillingAddress()?->getFirstName(),
                    'last_name' => $this->transaction->getBillingAddress()?->getLastName(),
                    'email' => $this->transaction->getBillingAddress()?->getEmail(),
                    'phone' => $this->transaction->getBillingAddress()?->getPhone(),
                    'address' => $this->transaction->getBillingAddress()?->getAddress(),
                    'city' => $this->transaction->getBillingAddress()?->getCity(),
                    'postal_code' => $this->transaction->getBillingAddress()?->getPostalCode(),
                    'country_code' => $this->transaction->getBillingAddress()?->getCountryCode(),
                ],
                'shipping_address' => [
                    'first_name' => $this->transaction->getShippingAddress()?->getFirstName(),
                    'last_name' => $this->transaction->getShippingAddress()?->getLastName(),
                    'email' => $this->transaction->getShippingAddress()?->getEmail(),
                    'phone' => $this->transaction->getShippingAddress()?->getPhone(),
                    'address' => $this->transaction->getShippingAddress()?->getAddress(),
                    'city' => $this->transaction->getShippingAddress()?->getCity(),
                    'postal_code' => $this->transaction->getShippingAddress()?->getPostalCode(),
                    'country_code' => $this->transaction->getShippingAddress()?->getCountryCode(),
                ],
            ];
        }

        if ($this->transaction->getItems()) {
            $default['item_details'] = array_map(fn (TransactionItem $item) => [
                'id' => $item->getId(),
                'price' => $item->getPrice(),
                'quantity' => $item->getQuantity(),
                'name' => $item->getName(),
            ], $this->transaction->getItems());
        }

        return $default;
    }
}
