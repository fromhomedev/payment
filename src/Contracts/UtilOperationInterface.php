<?php

declare(strict_types=1);

namespace FromHome\Payment\Contracts;

use FromHome\Payment\Input\CardBinFilterInput;

interface UtilOperationInterface
{
    /**
     * @return mixed
     */
    public function binInfo(CardBinFilterInput $input);
}
