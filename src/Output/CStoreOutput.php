<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Output;

final class CStoreOutput extends Output
{
    private string $paymentCode;

    public static function create(
        string $paymentCode,
        string $transactionId,
        ?string $orderId = null,
        ?string $status = null,
        ?float $amount = null,
        array $originalOutput = []
    ): self {
        $self = new self();

        $self->paymentCode = $paymentCode;
        $self->transactionId = $transactionId;
        $self->orderId = $orderId;
        $self->status = $status;
        $self->amount = $amount;
        $self->originalOutput = $originalOutput;

        return $self;
    }

    public function getPaymentCode(): string
    {
        return $this->paymentCode;
    }

    public static function fromArray(array $data): void
    {
    }
}
