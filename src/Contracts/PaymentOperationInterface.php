<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Contracts;

use Ziswapp\Payment\Input\CheckStatusTransactionInput;
use Ziswapp\Payment\Input\CancelPaymentTransactionInput;

interface PaymentOperationInterface
{
    /**
     * @return mixed
     */
    public function status(CheckStatusTransactionInput $input);

    /**
     * @return mixed
     */
    public function cancel(CancelPaymentTransactionInput $input);
}
