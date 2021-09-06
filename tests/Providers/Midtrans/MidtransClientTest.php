<?php

declare(strict_types=1);

namespace Ziswapp\Payment\Tests\Providers\Midtrans;

use Psl\Json;
use PHPUnit\Framework\TestCase;
use Ziswapp\Payment\Credentials;
use Ziswapp\Payment\ValueObject\CStore;
use Ziswapp\Payment\ValueObject\EWallet;
use Ziswapp\Payment\ValueObject\Customer;
use Ziswapp\Payment\ValueObject\Transaction;
use Ziswapp\Payment\Input\CardBinFilterInput;
use Ziswapp\Payment\Providers\Midtrans\Client;
use Ziswapp\Payment\ValueObject\VirtualAccount;
use Symfony\Component\HttpClient\MockHttpClient;
use Ziswapp\Payment\Exceptions\PaymentException;
use Ziswapp\Payment\Input\CStoreTransactionInput;
use Ziswapp\Payment\Input\EWalletTransactionInput;
use Ziswapp\Payment\Providers\Midtrans\MidtransClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Ziswapp\Payment\Input\VirtualAccountTransactionInput;

final class MidtransClientTest extends TestCase
{
    public function testCanCreatePermataVirtualAccount(): void
    {
        $json = <<<JSON
{
    "payment_type": "bank_transfer",
    "transaction_id": "ebbb3af7-dd57-4b6e-8026-72982d4b830c",
    "order_id": "4717083748679406811",
    "gross_amount": 1000000.0,
    "transaction_status": "pending",
    "permata_va_number": "070005961422194",
    "status_code": "201",
    "status_message": "Success, PERMATA VA transaction is successful",
    "currency": "IDR",
    "transaction_time": "2021-06-24 23:04:49",
    "fraud_status": "accept",
    "merchant_id": "M107080"
}
JSON;

        $httpClient = new MockHttpClient([
            new MockResponse($json),
        ], MidtransClient::SANDBOX_URL);

        $credentials = new Credentials(
            (string) \getenv('SANDBOX_MIDTRANS_KEY'),
            (string) \getenv('SANDBOX_MIDTRANS_SECRET'),
        );

        $client = new MidtransClient($credentials, [
            'isProduction' => false,
        ], null, null, $httpClient);

        $account = new VirtualAccount([
            'providerCode' => \Ziswapp\Payment\Enum\VirtualAccount::PERMATA(),
        ]);

        $transaction = $this->makeStubTransaction();
        $input = new VirtualAccountTransactionInput($account, $transaction);

        $output = $client->createVirtualAccount($input);

        $expected = Json\decode($json);
        $this->assertSame($expected['permata_va_number'], $output->getPaymentNumber());
        $this->assertSame($expected['transaction_status'], $output->getStatus());
        $this->assertSame($expected['order_id'], $output->getOrderId());
        $this->assertSame($expected['transaction_id'], $output->getTransactionId());
    }

    public function testCanCreateMandiriVirtualAccount(): void
    {
        $json = <<<JSON
{
    "payment_type": "echannel",
    "transaction_id": "d47e6443-d385-4c3b-b6e9-497effae968f",
    "order_id": "4717083748679406811",
    "gross_amount": 1000000.0,
    "transaction_status": "pending",
    "biller_code": "70012",
    "bill_key": "51273356767",
    "status_code": "201",
    "status_message": "OK, Mandiri Bill transaction is successful",
    "merchant_id": "M107080",
    "currency": "IDR",
    "transaction_time": "2021-06-24 23:22:11",
    "fraud_status": "accept"
}
JSON;

        $httpClient = new MockHttpClient([
            new MockResponse($json),
        ], MidtransClient::SANDBOX_URL);

        $credentials = new Credentials(
            (string) \getenv('SANDBOX_MIDTRANS_KEY'),
            (string) \getenv('SANDBOX_MIDTRANS_SECRET'),
        );

        $client = new MidtransClient($credentials, [
            'isProduction' => false,
        ], null, null, $httpClient);

        $account = new VirtualAccount([
            'providerCode' => \Ziswapp\Payment\Enum\VirtualAccount::MANDIRI(),
        ]);

        $transaction = $this->makeStubTransaction();
        $input = new VirtualAccountTransactionInput($account, $transaction);

        $output = $client->createVirtualAccount($input);

        $expected = Json\decode($json);
        $this->assertSame($expected['biller_code'] . $expected['bill_key'], $output->getPaymentNumber());
        $this->assertSame($expected['transaction_status'], $output->getStatus());
        $this->assertSame($expected['order_id'], $output->getOrderId());
        $this->assertSame($expected['transaction_id'], $output->getTransactionId());
    }

    public function testCanCreateBniVirtualAccount(): void
    {
        $json = <<<JSON
{
    "payment_type": "bank_transfer",
    "transaction_id": "518b4403-f4c2-44e9-b011-accebd895d05",
    "order_id": "4717083748679406811",
    "gross_amount": 1000000.0,
    "transaction_status": "pending",
    "va_numbers": [{ "bank": "bni", "va_number": "9880708054825361" }],
    "status_code": "201",
    "status_message": "Success, Bank Transfer transaction is created",
    "merchant_id": "M107080",
    "currency": "IDR",
    "transaction_time": "2021-06-24 23:05:21",
    "fraud_status": "accept"
}
JSON;

        $httpClient = new MockHttpClient([
            new MockResponse($json),
        ], MidtransClient::SANDBOX_URL);

        $credentials = new Credentials(
            (string) \getenv('SANDBOX_MIDTRANS_KEY'),
            (string) \getenv('SANDBOX_MIDTRANS_SECRET'),
        );

        $client = new MidtransClient($credentials, [
            'isProduction' => false,
        ], null, null, $httpClient);

        $account = new VirtualAccount([
            'providerCode' => \Ziswapp\Payment\Enum\VirtualAccount::BNI(),
        ]);

        $transaction = $this->makeStubTransaction();
        $input = new VirtualAccountTransactionInput($account, $transaction);

        $output = $client->createVirtualAccount($input);

        $expected = Json\decode($json);
        $this->assertSame($expected['va_numbers'][0]['va_number'], $output->getPaymentNumber());
        $this->assertSame($expected['transaction_status'], $output->getStatus());
        $this->assertSame($expected['order_id'], $output->getOrderId());
        $this->assertSame($expected['transaction_id'], $output->getTransactionId());
    }

    public function testCanCreateBcaVirtualAccount(): void
    {
        $json = <<<JSON
{
    "payment_type": "bank_transfer",
    "transaction_id": "7a7d9325-49a6-4038-bbd2-d8b77d009684",
    "order_id": "4717083748679406811",
    "gross_amount": 1000000.0,
    "transaction_status": "pending",
    "va_numbers": [{ "bank": "bca", "va_number": "107080298044" }],
    "status_code": "201",
    "status_message": "Success, Bank Transfer transaction is created",
    "merchant_id": "M107080",
    "currency": "IDR",
    "transaction_time": "2021-06-24 23:12:33",
    "fraud_status": "accept"
}
JSON;

        $httpClient = new MockHttpClient([
            new MockResponse($json),
        ], MidtransClient::SANDBOX_URL);

        $credentials = new Credentials(
            (string) \getenv('SANDBOX_MIDTRANS_KEY'),
            (string) \getenv('SANDBOX_MIDTRANS_SECRET'),
        );

        $client = new MidtransClient($credentials, [
            'isProduction' => false,
        ], null, null, $httpClient);

        $account = new VirtualAccount([
            'providerCode' => \Ziswapp\Payment\Enum\VirtualAccount::BNI(),
        ]);

        $transaction = $this->makeStubTransaction();
        $input = new VirtualAccountTransactionInput($account, $transaction);

        $output = $client->createVirtualAccount($input);

        $expected = Json\decode($json);
        $this->assertSame($expected['va_numbers'][0]['va_number'], $output->getPaymentNumber());
        $this->assertSame($expected['transaction_status'], $output->getStatus());
        $this->assertSame($expected['order_id'], $output->getOrderId());
        $this->assertSame($expected['transaction_id'], $output->getTransactionId());
    }

    public function testCanCreateBriVirtualAccount(): void
    {
        $json = <<<JSON
{
    "payment_type": "bank_transfer",
    "transaction_id": "22947af2-99da-41a2-8461-73d36004f50b",
    "order_id": "4717083748679406811",
    "gross_amount": 1000000.0,
    "transaction_status": "pending",
    "va_numbers": [{ "bank": "bri", "va_number": "8578000000111111" }],
    "status_code": "201",
    "status_message": "Success, Bank Transfer transaction is created",
    "merchant_id": "M107080",
    "currency": "IDR",
    "transaction_time": "2021-06-24 23:27:59",
    "fraud_status": "accept"
}
JSON;

        $httpClient = new MockHttpClient([
            new MockResponse($json),
        ], MidtransClient::SANDBOX_URL);

        $credentials = new Credentials(
            (string) \getenv('SANDBOX_MIDTRANS_KEY'),
            (string) \getenv('SANDBOX_MIDTRANS_SECRET'),
        );

        $client = new MidtransClient($credentials, [
            'isProduction' => false,
        ], null, null, $httpClient);

        $account = new VirtualAccount([
            'providerCode' => \Ziswapp\Payment\Enum\VirtualAccount::BNI(),
        ]);

        $transaction = $this->makeStubTransaction();
        $input = new VirtualAccountTransactionInput($account, $transaction);

        $output = $client->createVirtualAccount($input);

        $expected = Json\decode($json);
        $this->assertSame($expected['va_numbers'][0]['va_number'], $output->getPaymentNumber());
        $this->assertSame($expected['transaction_status'], $output->getStatus());
        $this->assertSame($expected['order_id'], $output->getOrderId());
        $this->assertSame($expected['transaction_id'], $output->getTransactionId());
    }

    public function testCanCreateGoPayPayment(): void
    {
        $json = <<<JSON
{
    "status_code": "201",
    "status_message": "GO-PAY billing created",
    "transaction_id": "e48447d1-cfa9-4b02-b163-2e915d4417ac",
    "order_id": "SAMPLE-ORDER-ID-01",
    "gross_amount": "10000.00",
    "payment_type": "gopay",
    "transaction_time": "2017-10-04 12:00:00",
    "transaction_status": "pending",
    "actions": [
        {
            "name": "generate-qr-code",
            "method": "GET",
            "url": "https://api.midtrans.com/v2/gopay/e48447d1-cfa9-4b02-b163-2e915d4417ac/qr-code"
        },
        {
            "name": "deeplink-redirect",
            "method": "GET",
            "url": "gojek://gopay/merchanttransfer?tref=1509110800474199656LMVO&amount=10000&activity=GP:RR&callback_url=someapps://callback?order_id=SAMPLE-ORDER-ID-01"
        },
        {
            "name": "get-status",
            "method": "GET",
            "url": "https://api.midtrans.com/v2/e48447d1-cfa9-4b02-b163-2e915d4417ac/status"
        },
        {
            "name": "cancel",
            "method": "POST",
            "url": "https://api.midtrans.com/v2/e48447d1-cfa9-4b02-b163-2e915d4417ac/cancel",
            "fields": []
        }
    ],
  "currency": "IDR"
}
JSON;

        $httpClient = new MockHttpClient([
            new MockResponse($json),
        ], MidtransClient::SANDBOX_URL);

        $credentials = new Credentials(
            (string) \getenv('SANDBOX_MIDTRANS_KEY'),
            (string) \getenv('SANDBOX_MIDTRANS_SECRET'),
        );

        $client = new MidtransClient($credentials, [
            'isProduction' => false,
        ], null, null, $httpClient);

        $eWallet = new EWallet([
            'providerCode' => \Ziswapp\Payment\Enum\EWallet::GOPAY(),
            'successUrl' => 'http://example/com',
        ]);

        $transaction = $this->makeStubTransaction();
        $input = new EWalletTransactionInput($eWallet, $transaction);

        $output = $client->createEWallet($input);

        $expected = Json\decode($json);
        $this->assertSame($expected['transaction_id'], $output->getTransactionId());
        $this->assertNull($output->getWebUrl());
        $this->assertNull($output->getMobileUrl());
        $this->assertSame($expected['actions'][0]['url'], $output->getQrCode());
        $this->assertSame($expected['actions'][1]['url'], $output->getDeeplinkUrl());
    }

    public function testCanCreateQrisPayment(): void
    {
        $json = <<<JSON
{
    "status_code": "201",
    "status_message": "QRIS transaction is created",
    "transaction_id": "0d8178e1-c6c7-4ab4-81a6-893be9d924ab",
    "order_id": "order03",
    "merchant_id": "M099098",
    "gross_amount": "275000.00",
    "currency": "IDR",
    "payment_type": "qris",
    "transaction_time": "2020-09-29 11:46:13",
    "transaction_status": "pending",
    "fraud_status": "accept",
    "acquirer": "gopay",
    "actions": [
        {
            "name": "generate-qr-code",
            "method": "GET",
            "url": "https://api.midtrans.com/v2/qris/0d8178e1-c6c7-4ab4-81a6-893be9d924ab/qr-code"
        }
    ]
}
JSON;

        $httpClient = new MockHttpClient([
            new MockResponse($json),
        ], MidtransClient::SANDBOX_URL);

        $credentials = new Credentials(
            (string) \getenv('SANDBOX_MIDTRANS_KEY'),
            (string) \getenv('SANDBOX_MIDTRANS_SECRET'),
        );

        $client = new MidtransClient($credentials, [
            'isProduction' => false,
        ], null, null, $httpClient);

        $eWallet = new EWallet([
            'providerCode' => \Ziswapp\Payment\Enum\EWallet::QRIS(),
            'successUrl' => 'http://example/com',
        ]);

        $transaction = $this->makeStubTransaction();
        $input = new EWalletTransactionInput($eWallet, $transaction);

        $output = $client->createEWallet($input);

        $expected = Json\decode($json);
        $this->assertSame($expected['transaction_id'], $output->getTransactionId());
        $this->assertNull($output->getWebUrl());
        $this->assertNull($output->getMobileUrl());
        $this->assertNull($output->getDeeplinkUrl());
        $this->assertSame($expected['actions'][0]['url'], $output->getQrCode());
    }

    public function testCanCreateShopeePayPayment(): void
    {
        $json = <<<JSON
{
    "status_code": "201",
    "status_message": "ShopeePay transaction is created",
    "channel_response_code": "0",
    "channel_response_message": "success",
    "transaction_id": "bb379c3a-218b-47c7-9b0b-25f71f0f1231",
    "order_id": "test-order-shopeepay-001",
    "merchant_id": "YON001",
    "gross_amount": "25000.00",
    "currency": "IDR",
    "payment_type": "shopeepay",
    "transaction_time": "2020-09-29 11:16:23",
    "transaction_status": "pending",
    "fraud_status": "accept",
    "actions": [
        {
            "name": "deeplink-redirect",
            "method": "GET",
            "url": "https://wsa.uat.wallet.airpay.co.id/universal-link/wallet/pay?deep_and_deferred=1&token=dFhkbmR1bTBIamhW5n7WPz2OrczCvb8_XiHliB9nROFMVByjtwKMAl6G0Ax0cMr79M4hwjs"
        }
    ]
}
JSON;

        $httpClient = new MockHttpClient([
            new MockResponse($json),
        ], MidtransClient::SANDBOX_URL);

        $credentials = new Credentials(
            (string) \getenv('SANDBOX_MIDTRANS_KEY'),
            (string) \getenv('SANDBOX_MIDTRANS_SECRET'),
        );

        $client = new MidtransClient($credentials, [
            'isProduction' => false,
        ], null, null, $httpClient);

        $eWallet = new EWallet([
            'providerCode' => \Ziswapp\Payment\Enum\EWallet::SHOPEEPAY(),
            'successUrl' => 'http://example/com',
        ]);

        $transaction = $this->makeStubTransaction();
        $input = new EWalletTransactionInput($eWallet, $transaction);

        $output = $client->createEWallet($input);

        $expected = Json\decode($json);
        $this->assertSame($expected['transaction_id'], $output->getTransactionId());
        $this->assertNull($output->getQrCode());
        $this->assertSame($expected['actions'][0]['url'], $output->getWebUrl());
        $this->assertSame($expected['actions'][0]['url'], $output->getMobileUrl());
        $this->assertSame($expected['actions'][0]['url'], $output->getDeeplinkUrl());
    }

    public function testCanCreateAlfamartAndIndomartPayment(): void
    {
        $json = <<<JSON
{
    "status_code": "201",
    "status_message": "Success, cstore transaction is successful",
    "transaction_id": "f1d381f8-7519-4139-b28f-81c6b3dc38ea",
    "order_id": "order05",
    "gross_amount": "10500.00",
    "payment_type": "cstore",
    "transaction_time": "2016-06-28 16:22:49",
    "transaction_status": "pending",
    "fraud_status": "accept",
    "payment_code": "010811223344",
    "store": "alfamart"
}
JSON;

        $httpClient = new MockHttpClient([
            new MockResponse($json),
        ], MidtransClient::SANDBOX_URL);

        $credentials = new Credentials(
            (string) \getenv('SANDBOX_MIDTRANS_KEY'),
            (string) \getenv('SANDBOX_MIDTRANS_SECRET'),
        );

        $client = new MidtransClient($credentials, [
            'isProduction' => false,
        ], null, null, $httpClient);

        $cStore = new CStore([
            'providerCode' => \Ziswapp\Payment\Enum\CStore::ALFAMART(),
            'message' => null,
        ]);

        $transaction = $this->makeStubTransaction();
        $input = new CStoreTransactionInput($cStore, $transaction);

        $output = $client->createConvenienceStore($input);

        $expected = Json\decode($json);
        $this->assertSame($expected['transaction_id'], $output->getTransactionId());
        $this->assertSame($expected['payment_code'], $output->getPaymentCode());
    }

    public function testHandleExceptionCreateVirtualAccount(): void
    {
        $json = <<<JSON
{
    "status_code" : "412",
    "status_message" : "Merchant cannot modify the status of the transaction"
}
JSON;

        $httpClient = new MockHttpClient([
            new MockResponse($json),
        ], MidtransClient::SANDBOX_URL);

        $credentials = new Credentials(
            (string) \getenv('SANDBOX_MIDTRANS_KEY'),
            (string) \getenv('SANDBOX_MIDTRANS_SECRET'),
        );

        $client = new MidtransClient($credentials, [
            'isProduction' => false,
        ], null, null, $httpClient);

        $transaction = $this->makeStubTransaction();

        $this->expectException(PaymentException::class);
        $this->expectExceptionMessage('Merchant cannot modify the status of the transaction');

        $account = new VirtualAccount([
            'providerCode' => \Ziswapp\Payment\Enum\VirtualAccount::BNI(),
        ]);
        $input = new VirtualAccountTransactionInput($account, $transaction);
        $client->createVirtualAccount($input);

        $this->expectException(PaymentException::class);
        $this->expectExceptionMessage('Merchant cannot modify the status of the transaction');
        $eWallet = new EWallet([
            'providerCode' => \Ziswapp\Payment\Enum\EWallet::SHOPEEPAY(),
            'successUrl' => 'http://example/com',
        ]);
        $input = new EWalletTransactionInput($eWallet, $transaction);
        $client->createEWallet($input);
    }

    public function testHandleExceptionCreateEWallet(): void
    {
        $json = <<<JSON
{
    "status_code" : "412",
    "status_message" : "Merchant cannot modify the status of the transaction"
}
JSON;

        $httpClient = new MockHttpClient([
            new MockResponse($json),
        ], MidtransClient::SANDBOX_URL);

        $credentials = new Credentials(
            (string) \getenv('SANDBOX_MIDTRANS_KEY'),
            (string) \getenv('SANDBOX_MIDTRANS_SECRET'),
        );

        $client = new MidtransClient($credentials, [
            'isProduction' => false,
        ], null, null, $httpClient);

        $transaction = $this->makeStubTransaction();

        $this->expectException(PaymentException::class);
        $this->expectExceptionMessage('Merchant cannot modify the status of the transaction');
        $eWallet = new EWallet([
            'providerCode' => \Ziswapp\Payment\Enum\EWallet::SHOPEEPAY(),
            'successUrl' => 'http://example/com',
        ]);
        $input = new EWalletTransactionInput($eWallet, $transaction);
        $client->createEWallet($input);
    }

    public function testHandleExceptionCreateCStore(): void
    {
        $json = <<<JSON
{
    "status_code" : "412",
    "status_message" : "Merchant cannot modify the status of the transaction"
}
JSON;

        $httpClient = new MockHttpClient([
            new MockResponse($json),
        ], MidtransClient::SANDBOX_URL);

        $credentials = new Credentials(
            (string) \getenv('SANDBOX_MIDTRANS_KEY'),
            (string) \getenv('SANDBOX_MIDTRANS_SECRET'),
        );

        $client = new MidtransClient($credentials, [
            'isProduction' => false,
        ], null, null, $httpClient);

        $transaction = $this->makeStubTransaction();

        $this->expectException(PaymentException::class);
        $this->expectExceptionMessage('Merchant cannot modify the status of the transaction');
        $cStore = new CStore([
            'providerCode' => \Ziswapp\Payment\Enum\CStore::INDOMART(),
        ]);
        $input = new CStoreTransactionInput($cStore, $transaction);
        $client->createConvenienceStore($input);
    }

    public function testBinFilterRequest(): void
    {
        $json = <<<JSON
{
    "data": {
        "country_name": "Indonesia",
        "country_code": "id",
        "brand": "visa",
        "bin_type": "CREDIT",
        "bin_class": "gold",
        "bin": "455633",
        "bank_code": "bca",
        "bank": "bank central asia"
    }
}
JSON;

        $httpClient = new MockHttpClient([
            new MockResponse($json),
        ], Client::SANDBOX_URL);

        $credentials = new Credentials(
            (string) \getenv('SANDBOX_MIDTRANS_KEY'),
            (string) \getenv('SANDBOX_MIDTRANS_SECRET'),
        );

        $client = new MidtransClient($credentials, [
            'isProduction' => false,
        ], null, null, $httpClient);

        $input = new CardBinFilterInput('455633');
        $output = $client->binInfo($input);

        $this->assertSame('CREDIT', $output->getType());
        $this->assertSame('455633', $output->getNumber());
    }

    protected function makeStubTransaction(): Transaction
    {
        return new Transaction([
            'id' => \random_int(0, PHP_INT_MAX),
            'amount' => 1000000,
            'currency' => 'IDR',
            'customer' => new Customer([
                'firstName' => 'Nuradiyana',
                'lastName' => 'Soleh',
            ]),
        ]);
    }
}
