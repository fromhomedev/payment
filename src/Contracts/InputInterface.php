<?php

declare(strict_types=1);

namespace FromHome\Payment\Contracts;

interface InputInterface
{
    public function requestBody(): array;
}
