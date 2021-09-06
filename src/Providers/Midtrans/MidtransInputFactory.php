<?php

declare(strict_types=1);

namespace FromHome\Payment\Providers\Midtrans;

use FromHome\Payment\Enum\CStore;
use FromHome\Payment\Enum\EWallet;
use FromHome\Payment\Enum\VirtualAccount;
use FromHome\Payment\Input\ChargeCardInput;
use FromHome\Payment\ValueObject\Transaction;
use FromHome\Payment\Contracts\InputInterface;
use FromHome\Payment\Input\CStoreTransactionInput;
use FromHome\Payment\Input\EWalletTransactionInput;
use FromHome\Payment\Contracts\CardInputFactoryInterface;
use FromHome\Payment\Input\VirtualAccountTransactionInput;
use FromHome\Payment\Contracts\PaymentInputFactoryInterface;
use FromHome\Payment\Providers\Midtrans\Concerns\InputRequestBody;

final class MidtransInputFactory implements InputInterface, PaymentInputFactoryInterface, CardInputFactoryInterface
{
    use InputRequestBody;

    protected ?array $params = null;

    protected Transaction $transaction;

    protected function __construct()
    {
    }

    public static function create(): self
    {
        return new self();
    }

    public function fromCStoreInput(CStoreTransactionInput $input): self
    {
        $this->setTransaction($input->getTransaction());

        $this->params = \array_merge($this->defaultParams(), [
            'payment_type' => 'cstore',
            'cstore' => [
                'store' => CStore::midtransCode(
                    $input->getCStore()->getProviderCode()
                ),
                'alfamart_free_text_1' => $input->getCStore()->getMessage(),
            ],
        ]);

        return $this;
    }

    public function fromVirtualAccountInput(VirtualAccountTransactionInput $input): self
    {
        $this->setTransaction($input->getTransaction());

        if ($input->getAccount()->getProviderCode() === VirtualAccount::MANDIRI()) {
            $this->params = \array_merge($this->defaultParams(), [
                'payment_type' => 'echannel',
                'echannel' => [
                    'bill_info1' => \sprintf('Payment for transaction %s', $input->getTransaction()->getId()),
                    'bill_info2' => 'debt',
                ],
            ]);
        } else {
            $this->params = \array_merge($this->defaultParams(), [
                'bank_transfer' => [
                    'bank' => VirtualAccount::midtransCode(
                        $input->getAccount()->getProviderCode()
                    ),
                    'va_number' => $input->getAccount()->getNumber(),
                ],
                'payment_type' => 'bank_transfer',
            ]);
        }

        return $this;
    }

    public function fromEWalletInput(EWalletTransactionInput $input): self
    {
        $this->setTransaction($input->getTransaction());

        $this->params = \array_merge($this->defaultParams(), [
            'payment_type' => EWallet::midtransCode(
                $input->getWallet()->getProviderCode()
            ),
            'qris' => [
                'acquirer' => 'gopay',
            ],
            'gopay' => [
                'enable_callback' => true,
                'callback_url' => $input->getWallet()->getSuccessUrl(),
            ],
            'shopeepay' => [
                'callback_url' => $input->getWallet()->getSuccessUrl(),
            ],
        ]);

        return $this;
    }

    public function fromChargeInput(ChargeCardInput $input): self
    {
        $this->setTransaction($input->getTransaction());

        $this->params = \array_merge($this->defaultParams(), [
            'payment_type' => 'credit_card',
            'credit_card' => [
                'token_id' => $input->getToken(),
                'authentication' => $input->isAuthentication(),
                'save_token_id' => $input->isSavedToken(),
                'bins' => $input->getAllowedBins(),
            ],
        ]);

        return $this;
    }

    public function requestBody(): array
    {
        if ($this->params) {
            return $this->params;
        }

        throw new \LogicException('Params or input must be set.');
    }

    protected function setTransaction(Transaction $transaction): void
    {
        $this->transaction = $transaction;
    }
}
