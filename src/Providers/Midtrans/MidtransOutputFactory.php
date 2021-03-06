<?php

declare(strict_types=1);

namespace FromHome\Payment\Providers\Midtrans;

use DateTime;
use Psl\Type;
use FromHome\Payment\Output\CStoreOutput;
use FromHome\Payment\Output\EWalletOutput;
use FromHome\Payment\Output\ChargeCardOutput;
use FromHome\Payment\Output\CheckStatusOutput;
use FromHome\Payment\Output\CardBinFilterOutput;
use FromHome\Payment\Output\VirtualAccountOutput;
use FromHome\Payment\Contracts\OutputFactoryInterface;
use FromHome\Payment\Contracts\CardOutputFactoryInterface;

final class MidtransOutputFactory implements OutputFactoryInterface, CardOutputFactoryInterface
{
    private function __construct()
    {
    }

    public static function create(): self
    {
        return new self();
    }

    public function fromStatusArray(array $data): CheckStatusOutput
    {
        $data = Type\shape([
            'transaction_id' => Type\non_empty_string(),
            'transaction_status' => Type\non_empty_string(),
        ], true)->coerce($data);

        return CheckStatusOutput::create(
            $data['transaction_id'],
            $data['transaction_status'],
            $data
        );
    }

    public function fromVirtualAccountArray(array $data): VirtualAccountOutput
    {
        $data = Type\shape([
            'payment_type' => Type\non_empty_string(),
            'transaction_id' => Type\non_empty_string(),
            'order_id' => Type\non_empty_string(),
            'gross_amount' => Type\float(),
            'transaction_status' => Type\non_empty_string(),
            'va_numbers' => Type\optional(Type\dict(Type\array_key(), Type\shape([
                'bank' => Type\non_empty_string(),
                'va_number' => Type\non_empty_string(),
            ]))),
            'permata_va_number' => Type\optional(Type\non_empty_string()),
            'biller_code' => Type\optional(Type\non_empty_string()),
            'bill_key' => Type\optional(Type\non_empty_string()),
        ], true)->coerce($data);

        $paymentNumber = '';

        if (\array_key_exists('permata_va_number', $data)) {
            $paymentNumber = $data['permata_va_number'];
        }

        if (\array_key_exists('va_numbers', $data)) {
            $paymentNumber = $data['va_numbers'][0]['va_number'];
        }

        if (\array_key_exists('biller_code', $data) && \array_key_exists('bill_key', $data)) {
            $paymentNumber = $data['biller_code'] . $data['bill_key'];
        }

        return VirtualAccountOutput::create(
            $paymentNumber,
            $data['transaction_id'],
            $data['order_id'],
            $data['transaction_status'],
            $data['gross_amount'],
            $data
        );
    }

    public function fromCStoreArray(array $data): CStoreOutput
    {
        $data = Type\shape([
            'payment_type' => Type\non_empty_string(),
            'transaction_id' => Type\non_empty_string(),
            'order_id' => Type\non_empty_string(),
            'gross_amount' => Type\float(),
            'transaction_status' => Type\non_empty_string(),
            'payment_code' => Type\non_empty_string(),
        ], true)->coerce($data);

        return CStoreOutput::create(
            $data['payment_code'],
            $data['transaction_id'],
            $data['order_id'],
            $data['transaction_status'],
            $data['gross_amount'],
            $data
        );
    }

    public function fromEWalletArray(array $data): EWalletOutput
    {
        $data = Type\shape([
            'payment_type' => Type\non_empty_string(),
            'transaction_id' => Type\non_empty_string(),
            'order_id' => Type\non_empty_string(),
            'gross_amount' => Type\float(),
            'actions' => Type\dict(Type\array_key(), Type\shape([
                'name' => Type\non_empty_string(),
                'url' => Type\non_empty_string(),
            ])),
            'transaction_status' => Type\non_empty_string(),
        ], true)->coerce($data);

        $webUrl = null;
        $mobileUrl = null;
        $qrCode = null;
        $deeplinkUrl = null;

        switch ($data['payment_type']) {
            case 'qris':
                $webUrl = null;
                $mobileUrl = null;
                $qrCode = $data['actions'][0]['url'];
                $deeplinkUrl = null;
                break;
            case 'gopay':
                $webUrl = null;
                $mobileUrl = null;
                $qrCode = $data['actions'][0]['url'];
                $deeplinkUrl = $data['actions'][1]['url'];
                break;
            case 'shopeepay':
                $qrCode = null;
                $deeplinkUrl = $data['actions'][0]['url'];
                $webUrl = $data['actions'][0]['url'];
                $mobileUrl = $data['actions'][0]['url'];
                break;
        }

        return EWalletOutput::create(
            $data['transaction_id'],
            $data['order_id'],
            $data['transaction_status'],
            $data['gross_amount'],
            $deeplinkUrl,
            $qrCode,
            $webUrl,
            $mobileUrl,
            $data
        );
    }

    public function fromFilterBinArray(array $data): CardBinFilterOutput
    {
        $data = Type\shape([
            'data' => Type\shape([
                'bin' => Type\non_empty_string(),
                'bin_type' => Type\non_empty_string(),
            ], true),
        ])->coerce($data);

        return CardBinFilterOutput::create($data['data']['bin'], $data['data']['bin_type'], $data['data']);
    }

    public function fromChargeArray(array $data): ChargeCardOutput
    {
        $data = Type\shape([
            'transaction_id' => Type\non_empty_string(),
            'order_id' => Type\non_empty_string(),
            'redirect_url' => Type\optional(Type\nullable(Type\string())),
            'gross_amount' => Type\non_empty_string(),
            'fraud_status' => Type\non_empty_string(),
            'transaction_status' => Type\non_empty_string(),
            'transaction_time' => Type\non_empty_string(),
        ], true)->coerce($data);

        /** @noinspection PhpUnhandledExceptionInspection */
        return ChargeCardOutput::create(
            $data['transaction_id'],
            $data['order_id'],
            $data['transaction_status'],
            $data['fraud_status'],
            (float) $data['gross_amount'],
            new DateTime($data['transaction_time']),
            $data['redirect_url'] ?? null,
            $data
        );
    }
}
