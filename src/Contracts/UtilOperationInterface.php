<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Contracts;

use Ziswapp\Payment\Input\CardBinFilterInput;

interface UtilOperationInterface
{
    /**
     * @return mixed
     */
    public function binInfo(CardBinFilterInput $input);
}
