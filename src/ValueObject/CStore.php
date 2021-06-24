<?php

declare(strict_types=1);

namespace Ziswapp\Payment\ValueObject;

use Psl\Type;

final class CStore
{
    private string $providerCode;

    private ?string $message;

    private array $input;

    /**
     * @param array{
     *  providerCode: string,
     *  message?: string
     * } $input
     */
    public function __construct(array $input)
    {
        $input = Type\shape([
            'providerCode' => Type\non_empty_string(),
            'message' => Type\optional(Type\union(Type\null(), Type\non_empty_string())),
        ], true)->coerce($input);

        $this->providerCode = $input['providerCode'];
        $this->message = $input['message'] ?? '';
        $this->input = $input;
    }

    public function getProviderCode(): string
    {
        return $this->providerCode;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getInput(): array
    {
        return $this->input;
    }
}
