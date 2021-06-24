<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Input;

use Ziswapp\Payment\ValueObject\CStore;
use Ziswapp\Payment\Enum\CStore as Enum;
use Ziswapp\Payment\ValueObject\Transaction;

final class CStoreInput extends Input
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
