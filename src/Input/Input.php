<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Input;

use Ziswapp\Payment\ValueObject\Transaction;

abstract class Input
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
