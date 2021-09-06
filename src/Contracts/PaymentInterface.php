<?php

declare(strict_types=1);

namespace FromHome\Payment\Contracts;

use FromHome\Payment\Input\CStoreTransactionInput;
use FromHome\Payment\Input\EWalletTransactionInput;
use FromHome\Payment\Input\VirtualAccountTransactionInput;

interface PaymentInterface
{
    public const VERSION = '1.0';

    /**
     * @return mixed
     */
    public function createVirtualAccount(VirtualAccountTransactionInput $input);

    /**
     * @return mixed
     */
    public function createEWallet(EWalletTransactionInput $input);

    /**
     * @return mixed
     */
    public function createConvenienceStore(CStoreTransactionInput $input);
}
