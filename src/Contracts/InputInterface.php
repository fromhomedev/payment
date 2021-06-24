<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Contracts;

interface InputInterface
{
    public function requestBody(): array;
}
