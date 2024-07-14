# Tamkeen SDK

<a href="https://github.com/pcsoftgroup/tamkeen-php-sdk/actions"><img src="https://github.com/pcsoftgroup/tamkeen-php-sdk/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/pcsoftgroup/tamkeen-php-sdk"><img src="https://img.shields.io/packagist/dt/pcsoftgroup/tamkeen-php-sdk" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/pcsoftgroup/tamkeen-php-sdk"><img src="https://img.shields.io/packagist/v/pcsoftgroup/tamkeen-php-sdk" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/pcsoftgroup/tamkeen-php-sdk"><img src="https://img.shields.io/packagist/l/pcsoftgroup/tamkeen-php-sdk" alt="License"></a>

## Introduction

The [Tamkeen](https://tamkeen.com.ye) SDK provides an expressive interface for interacting with Tamkeen's API.

### Installation

To install the SDK in your project you need to add the package via composer:

```json
{
    "require": {
        "ghamdannosary/tamkeen-php-sdk": "dev"
    },
    "repositories": [
        {
            "type": "package",
            "package": {
                "name": "ghamdannosary/tamkeen-php-sdk",
                "version": "dev",
                "source": {
                    "url": "https://github.com/ghamdannosary/tamkeen-php-sdk.git",
                    "type": "git",
                    "reference": "main"
                }
            }
        }
    ]
}
```

### Upgrading

When upgrading to a new major version of Tamkeen SDK, it's important that you carefully review [the upgrade guide](https://github.com/ghamdannosary/tamkeen-php-sdk/blob/main/UPGRADE.md).

### Basic Usage

You can create an instance of the SDK like so:

```php
use  Techsoft\Cashpay\Resources\Currency;
use  Techsoft\Cashpay\Resources\OperationStatusType;
use  Techsoft\Cashpay\Tamkeen;

$tamkeen = new Tamkeen();
$tamkeen->setUsername('YOUR_USERNAME');
$tamkeen->setPassword('YOUR_PASSWORD');
$tamkeen->setServiceProviderId('YOUR_SERVICE_PROVIDER_ID');
$tamkeen->setEncryptionKey('YOUR_ENCRYPTION_KEY');
$tamkeen->setCertificate('YOUR_CERTIFICATE_PATH', 'YOUR_CERTIFICATE_PASSWORD');//you will need it in production env
$tamkeen->build('YOUR_PORT');

$payment = $tamkeen->createPayment(
    phone:'77xxxxxxx',
    CustomerCashPayCode:123,
    amount:100,
    currency:Currency::YER,
);

// Confirm payment
$tamkeen->confirmPayment(
    ref:$payment->TransactionRef,
    otp:123456, //YOUR_OTP_HERE
);

// Confirm payment
$tamkeen->checkPayment(
    ref:$payment->TransactionRef,
    type:OperationStatusType::CONFIRMED,
);
```

## Contributing

Thank you for considering contributing to Tamkeen SDK! You can read the contribution guide [here](.github/CONTRIBUTING.md).

## Code of Conduct

In order to ensure that the PCsoft community is welcoming to all, please review and abide by the [Code of Conduct](https://pcsoftgroup.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

Please review [our security policy](https://github.com/pcsoftgroup/tamkeen-php-sdk/security/policy) on how to report security vulnerabilities.

## License

PCsoft Tamkeen SDK is open-sourced software licensed under the [MIT license](LICENSE.md).
