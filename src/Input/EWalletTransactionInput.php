<?php

declare(strict_types=1);

namespace FromHome\Payment\Input;

use FromHome\Payment\ValueObject\EWallet;
use FromHome\Payment\Enum\EWallet as Enum;
use FromHome\Payment\ValueObject\Transaction;

final class EWalletTransactionInput extends TransactionInput
{
    protected EWallet $wallet;

    public function __construct(EWallet $wallet, Transaction $transaction)
    {
        $this->wallet = $wallet;

        parent::__construct($transaction);

        if (! \in_array($this->wallet->getProviderCode(), Enum::toArray(), true)) {
            throw new \LogicException('Invalid EWallet providerCode');
        }
    }

    public function getWallet(): EWallet
    {
        return $this->wallet;
    }
}
