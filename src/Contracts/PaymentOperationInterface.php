<?php

declare(strict_types=1);

namespace FromHome\Payment\Contracts;

use FromHome\Payment\Input\CheckStatusTransactionInput;
use FromHome\Payment\Input\CancelPaymentTransactionInput;

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
