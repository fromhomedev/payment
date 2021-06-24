<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Contracts;

interface CredentialsInterface
{
    public function getKey(): string;

    public function getSecret(): string;
}
