<?php

declare(strict_types=1);

namespace FromHome\Payment\Input;

use FromHome\Payment\ValueObject\Transaction;

final class CheckStatusTransactionInput extends TransactionInput
{
    private ?string $providerCode = null;

    public function __construct(Transaction $transaction, ?string $providerCode = null)
    {
        parent::__construct($transaction);

        $this->providerCode = $providerCode;
    }

    public function getProviderCode(): ?string
    {
        return $this->providerCode;
    }
}
