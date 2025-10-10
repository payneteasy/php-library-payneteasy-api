# PHP Library for PaynetEasy API integration [![Build Status](https://travis-ci.org/payneteasy/php-library-payneteasy-api.png?branch=master)](https://travis-ci.org/payneteasy/php-library-paynet)
## Available functionality

This library allows to make payments using [PaynetEasy Merchant API](http://doc.payneteasy.com/). For now, the following payment methods are implemented:
- [x] [Account Verification](https://doc.payneteasy.eu/integration/api_use_cases/server_to_server_account_verification.html)
- [x] [Sale Transactions](https://doc.payneteasy.eu/integration/api_use_cases/server_to_server_sale.html)
- [x] [Preauth/Capture Transactions](https://doc.payneteasy.eu/integration/api_use_cases/server_to_server_preauth_capture_and_cancel.html)
- [x] [Transfer Transactions](https://doc.payneteasy.eu/integration/api_use_cases/server_to_server_transfer.html)
- [x] [Return Transactions](https://doc.payneteasy.eu/integration/api_use_cases/return_transaction.html)
- [x] [Recurrent Transactions](https://doc.payneteasy.eu/integration/api_use_cases/recurring_sale.html)
- [x] [Payment Form Integration](https://doc.payneteasy.eu/integration/api_use_cases/sale_form.html)
- [x] [Connecting Party Callbacks](https://doc.payneteasy.eu/integration/API_commands/merchant_callback_parameters.html)

## System requirements

* PHP 5.4 - 5.6
* [curl extension](http://php.net/manual/en/book.curl.php)

## Install

1. [Install composer](http://getcomposer.org/doc/00-intro.md), if it is not installed yet
2. Chdir to project directory: `cd my/project/directory`
3. Create project file for Composer, if it does not exist yet: `composer init`
4. Add the libraty to project dependencies: `composer require payneteasy/php-library-payneteasy-api:dev-master --prefer-dist`

## Run tests

1. Chdir to library directory: `cd vendor/payneteasy/php-library-payneteasy-api/`
2. Run tests: `phpunit -c test/phpunit.xml test`

## Usage

* [Library simple usage example](00-basic-tutorial.md)
* [Internal library structure](01-library-internals.md)
    * [Data storage and exchange classes, PaynetEasy\PaynetEasyApi\PaymentData](library-internals/00-payment-data.md)
    * [Library frontend, PaynetEasy\PaynetEasyApi\PaymentProcessor](library-internals/01-payment-processor.md)
    * [Data validator, PaynetEasy\PaynetEasyApi\Util\Validator](library-internals/02-validator.md)
    * [Property chains handling class, PaynetEasy\PaynetEasyApi\Util\PropertyAccessor](library-internals/03-property-accessor.md)
* [Integrating different payment scenarios](02-payment-scenarios.md)
    * [Account verification](payment-scenarios/07-account-verification.md)
    * [Sale transactions](payment-scenarios/00-sale-transactions.md)
    * [Preauth/Capture Transactions](payment-scenarios/01-preauth-capture-transactions.md)
    * [Transfer Transactions](payment-scenarios/02-transfer-transactions.md)
    * [Return Transactions](payment-scenarios/03-return-transactions.md)
    * [Recurrent Transactions](payment-scenarios/04-recurrent-transactions.md)
    * [Payment Form Integration](payment-scenarios/05-payment-form-integration.md)
    * [Merchant Callbacks](payment-scenarios/06-merchant-callbacks.md)
