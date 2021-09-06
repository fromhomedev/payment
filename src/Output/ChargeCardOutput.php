<?php

declare(strict_types=1);

namespace FromHome\Payment\Output;

use DateTimeInterface;

final class ChargeCardOutput extends TransactionOutput
{
    private ?string $redirectUrl = null;

    private string $fraudStatus;

    private DateTimeInterface $transactionDate;

    public static function create(
        string $transactionId,
        string $orderId,
        string $status,
        string $fraudStatus,
        float $amount,
        DateTimeInterface $transactionDate,
        ?string $redirectUrl = null,
        array $originalOutput = []
    ): self {
        $self = new self();

        $self->transactionId = $transactionId;
        $self->orderId = $orderId;
        $self->status = $status;
        $self->fraudStatus = $fraudStatus;
        $self->amount = $amount;
        $self->redirectUrl = $redirectUrl;
        $self->transactionDate = $transactionDate;
        $self->originalOutput = $originalOutput;

        return $self;
    }

    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }

    public function getFraudStatus(): string
    {
        return $this->fraudStatus;
    }

    public function getTransactionDate(): DateTimeInterface
    {
        return $this->transactionDate;
    }
}
