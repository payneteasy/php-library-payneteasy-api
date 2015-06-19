# PHP Library for PaynetEasy API integration [![Build Status](https://travis-ci.org/payneteasy/php-library-payneteasy-api.png?branch=master)](https://travis-ci.org/payneteasy/php-library-paynet)
## Available functionality

This library allows to make payments using [PaynetEasy Merchant API](http://doc.payneteasy.com/). For now, the following payment methods are implemented:
- [x] [Sale Transactions](http://doc.payneteasy.com/doc/sale-transactions.htm)
- [x] [Preauth/Capture Transactions](http://doc.payneteasy.com/doc/preauth-capture-transactions.htm)
- [x] [Transfer Transactions](http://doc.payneteasy.com/doc/transfer-transactions.htm)
- [x] [Return Transactions](http://doc.payneteasy.com/doc/return-transactions.htm)
- [x] [Recurrent Transactions](http://doc.payneteasy.com/doc/recurrent-transactions.htm)
- [x] [Payment Form Integration](http://doc.payneteasy.com/doc/payment-form-integration.htm)
- [ ] [Buy Now Button integration](http://doc.payneteasy.com/doc/buy-now-button-integration.htm)
- [ ] [eCheck integration](http://doc.payneteasy.com/doc/echeck-integration.htm)
- [ ] [Western Union Integration](http://doc.payneteasy.com/doc/money-transfer-systems.htm)
- [ ] [Bitcoin Integration](http://doc.payneteasy.com/doc/bitcoin-integration.htm)
- [ ] [Loan Integration](http://doc.payneteasy.com/doc/loan-integration.htm)
- [ ] [Qiwi Integration](http://doc.payneteasy.com/doc/qiwi-integration.htm)
- [x] [Merchant Callbacks](http://doc.payneteasy.com/doc/merchant-callbacks.htm)

## System requirements

* PHP 5.3 - 5.5
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
    * [Sale transactions](payment-scenarios/00-sale-transactions.md)
    * [Preauth/Capture Transactions](payment-scenarios/01-preauth-capture-transactions.md)
    * [Transfer Transactions](payment-scenarios/02-transfer-transactions.md)
    * [Return Transactions](payment-scenarios/03-return-transactions.md)
    * [Recurrent Transactions](payment-scenarios/04-recurrent-transactions.md)
    * [Payment Form Integration](payment-scenarios/05-payment-form-integration.md)
    * [Merchant Callbacks](payment-scenarios/06-merchant-callbacks.md)
