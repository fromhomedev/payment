<?php

declare(strict_types=1);

namespace FromHome\Payment\ValueObject;

use Psl\Type;

final class EWallet
{
    private string $providerCode;

    private string $successUrl;

    private ?string $failureUrl;

    private ?string $mobileNumber;

    private array $input;

    /**
     * @param array{
     *  providerCode: string,
     *  successUrl: string,
     *  mobileNumber?: string,
     *  failureUrl?: string
     * } $input
     */
    public function __construct(array $input)
    {
        $input = Type\shape([
            'providerCode' => Type\non_empty_string(),
            'successUrl' => Type\non_empty_string(),
            'mobileNumber' => Type\optional(Type\non_empty_string()),
            'failureUrl' => Type\optional(Type\non_empty_string()),
        ], true)->coerce($input);

        $this->providerCode = $input['providerCode'];
        $this->successUrl = $input['successUrl'];
        $this->mobileNumber = $input['mobileNumber'] ?? null;
        $this->failureUrl = $input['failureUrl'] ?? null;

        $this->input = $input;
    }

    public function getSuccessUrl(): string
    {
        return $this->successUrl;
    }

    public function getMobileNumber(): mixed
    {
        return $this->mobileNumber;
    }

    public function getFailureUrl(): mixed
    {
        return $this->failureUrl;
    }

    public function getProviderCode(): string
    {
        return $this->providerCode;
    }

    public function getInput(): array
    {
        return $this->input;
    }
}
