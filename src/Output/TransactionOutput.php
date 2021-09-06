<?php

declare(strict_types=1);

namespace FromHome\Payment\Output;

abstract class TransactionOutput extends Output
{
    protected string $transactionId;

    protected ?string $orderId = null;

    protected ?string $status = null;

    protected ?float $amount = null;

    public function setOriginalOutput(array $originalOutput): void
    {
        $this->originalOutput = $originalOutput;
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }
}
