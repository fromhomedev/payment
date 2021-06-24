<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Providers\Xendit;

use Ziswapp\Payment\Enum\EWallet;
use Ziswapp\Payment\Input\CStoreInput;
use Ziswapp\Payment\Input\EWalletInput;
use Ziswapp\Payment\Enum\VirtualAccount;
use Ziswapp\Payment\ValueObject\Customer;
use Ziswapp\Payment\Contracts\InputInterface;
use Ziswapp\Payment\Input\VirtualAccountInput;
use Ziswapp\Payment\Contracts\PaymentInputFactoryInterface;

final class XenditInputFactory implements InputInterface, PaymentInputFactoryInterface
{
    private ?array $params = null;

    private function __construct()
    {
    }

    public static function create(): self
    {
        return new self();
    }

    public function fromCStoreInput(CStoreInput $input): self
    {
        return self::create();
    }

    public function fromVirtualAccountInput(VirtualAccountInput $input): self
    {
        /** @var Customer|null $customer */
        $customer = $input->getTransaction()->getCustomer();

        $name = $customer ? \sprintf('%s %s', $customer->getFirstName(), $customer->getLastName()) : 'No Name';

        $this->params = [
            'external_id' => $input->getTransaction()->getId(),
            'bank_code' => VirtualAccount::xenditCode(
                $input->getAccount()->getProviderCode()
            ),
            'name' => $name,
            'virtual_account_number' => $input->getAccount()->getNumber(),
            'suggested_amount' => $input->getTransaction()->getAmount(),
            'expected_amount' => $input->getTransaction()->getAmount(),
        ];

        return $this;
    }

    public function fromEWalletInput(EWalletInput $input): self
    {
        $this->params = [
            'reference_id' => $input->getTransaction()->getId(),
            'currency' => $input->getTransaction()->getCurrency(),
            'amount' => $input->getTransaction()->getAmount(),
            'checkout_method' => 'ONE_TIME_PAYMENT',
            'channel_code' => EWallet::xenditCode(
                $input->getWallet()->getProviderCode()
            ),
            'channel_properties' => [
                'mobile_number' => $input->getWallet()->getMobileNumber(),
                'success_redirect_url' => $input->getWallet()->getSuccessUrl(),
                'failure_redirect_url' => $input->getWallet()->getFailureUrl(),
            ],
        ];

        return $this;
    }

    public function requestBody(): array
    {
        if ($this->params) {
            return $this->params;
        }

        throw new \LogicException('Params or input must be set.');
    }
}
