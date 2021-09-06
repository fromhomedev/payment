<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Input;

use Ziswapp\Payment\ValueObject\Transaction;
use Ziswapp\Payment\ValueObject\VirtualAccount;
use Ziswapp\Payment\Enum\VirtualAccount as Enum;

final class VirtualAccountTransactionInput extends TransactionInput
{
    protected VirtualAccount $account;

    protected Transaction $transaction;

    public function __construct(VirtualAccount $account, Transaction $transaction)
    {
        $this->account = $account;

        parent::__construct($transaction);

        if (! \in_array($this->account->getProviderCode(), Enum::toArray(), true)) {
            throw new \LogicException('Invalid Virtual Account providerCode');
        }
    }

    public function getAccount(): VirtualAccount
    {
        return $this->account;
    }
}
