<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Contracts;

use Ziswapp\Payment\Input\ChargeCardInput;

interface CardInputFactoryInterface extends InputInterface
{
    public function fromChargeInput(ChargeCardInput $input): self;
}
