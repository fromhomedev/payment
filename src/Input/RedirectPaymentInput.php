<?php

declare(strict_types=1);

namespace FromHome\Payment\Input;

use FromHome\Payment\ValueObject\Transaction;

final class RedirectPaymentInput extends TransactionInput
{
    private string $successUrl;

    private ?array $channels = null;

    private ?string $vaNumber = null;

    public function __construct(Transaction $transaction, string $successUrl, ?array $channels = null, ?string $vaNumber = null)
    {
        $this->channels = $channels;
        $this->successUrl = $successUrl;
        $this->vaNumber = $vaNumber;

        parent::__construct($transaction);
    }

    public function getChannels(): ?array
    {
        return $this->channels;
    }

    public function getSuccessUrl(): string
    {
        return $this->successUrl;
    }

    public function getNumber(): ?string
    {
        return $this->vaNumber;
    }
}
