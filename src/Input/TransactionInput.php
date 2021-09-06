<?php

declare(strict_types=1);

namespace FromHome\Payment\Input;

use FromHome\Payment\ValueObject\Transaction;

abstract class TransactionInput
{
    protected Transaction $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function getTransaction(): Transaction
    {
        return $this->transaction;
    }
}
