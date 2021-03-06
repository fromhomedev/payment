<?php

declare(strict_types=1);

namespace FromHome\Payment\Input;

use FromHome\Payment\ValueObject\Transaction;

final class CancelPaymentTransactionInput extends TransactionInput
{
    private string $providerCode;

    public function __construct(string $providerCode, Transaction $transaction)
    {
        parent::__construct($transaction);

        $this->providerCode = $providerCode;
    }

    public function getProviderCode(): string
    {
        return $this->providerCode;
    }
}
