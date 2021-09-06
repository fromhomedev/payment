<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Input;

use Ziswapp\Payment\ValueObject\Transaction;

final class ChargeCardInput extends TransactionInput
{
    private string $token;

    private bool $authentication;

    private bool $savedToken;

    private array $allowedBins;

    public function __construct(Transaction $transaction, string $token, bool $authentication, bool $savedToken = false, array $allowedBins = [])
    {
        $this->token = $token;
        $this->authentication = $authentication;
        $this->savedToken = $savedToken;
        $this->allowedBins = $allowedBins;

        parent::__construct($transaction);
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function isAuthentication(): bool
    {
        return $this->authentication;
    }

    public function isSavedToken(): bool
    {
        return $this->savedToken;
    }

    public function getAllowedBins(): array
    {
        return $this->allowedBins;
    }
}
