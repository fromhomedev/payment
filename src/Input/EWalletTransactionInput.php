<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Input;

use Ziswapp\Payment\ValueObject\EWallet;
use Ziswapp\Payment\Enum\EWallet as Enum;
use Ziswapp\Payment\ValueObject\Transaction;

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
