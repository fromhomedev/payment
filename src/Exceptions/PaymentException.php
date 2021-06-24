<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Exceptions;

use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;

class PaymentException extends \RuntimeException implements ClientExceptionInterface
{
    private ResponseInterface $response;

    public function __construct(ResponseInterface $response, int $code, ?string $message = null)
    {
        $this->response = $response;

        $url = $response->getInfo('url');

        $message = $message ?? sprintf('HTTP %d returned for "%s".', $code, $url);

        parent::__construct($message, $code);
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
