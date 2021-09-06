<?php

declare(strict_types=1);

namespace FromHome\Payment\Input;

use FromHome\Payment\ValueObject\CStore;
use FromHome\Payment\Enum\CStore as Enum;
use FromHome\Payment\ValueObject\Transaction;

final class CStoreTransactionInput extends TransactionInput
{
    protected CStore $cStore;

    public function __construct(CStore $cStore, Transaction $transaction)
    {
        $this->cStore = $cStore;

        parent::__construct($transaction);

        if (! \in_array($this->cStore->getProviderCode(), Enum::toArray(), true)) {
            throw new \LogicException('Invalid CStore providerCode');
        }
    }

    public function getCStore(): CStore
    {
        return $this->cStore;
    }
}
