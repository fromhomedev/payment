<?php

declare(strict_types=1);

namespace FromHome\Payment\Contracts;

use FromHome\Payment\Input\RedirectPaymentInput;

interface RedirectPaymentInterface
{
    /**
     * @return mixed
     */
    public function createUrl(RedirectPaymentInput $input);
}
