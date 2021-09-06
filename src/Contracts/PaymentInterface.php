<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Contracts;

use Ziswapp\Payment\Input\CStoreTransactionInput;
use Ziswapp\Payment\Input\EWalletTransactionInput;
use Ziswapp\Payment\Input\VirtualAccountTransactionInput;

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
