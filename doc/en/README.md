# PHP Library for PaynetEasy API integration [![Build Status](https://travis-ci.org/payneteasy/php-library-payneteasy-api.png?branch=master)](https://travis-ci.org/payneteasy/php-library-paynet)
## Available functionality

This library allows to make payments using [merchant PaynetEasy API](http://wiki.payneteasy.com/index.php/PnE:Merchant_API). For now, the following payment methods are implemented:
- [x] [Sale Transactions](http://wiki.payneteasy.com/index.php/PnE:Sale_Transactions)
- [x] [Preauth/Capture Transactions](http://wiki.payneteasy.com/index.php/PnE:Preauth/Capture_Transactions)
- [x] [Transfer Transactions](http://wiki.payneteasy.com/index.php/PnE:Transfer_Transactions)
- [x] [Return Transactions](http://wiki.payneteasy.com/index.php/PnE:Return_Transactions)
- [x] [Recurrent Transactions](http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions)
- [x] [Payment Form Integration](http://wiki.payneteasy.com/index.php/PnE:Payment_Form_integration)
- [ ] [Buy Now Button integration](http://wiki.payneteasy.com/index.php/PnE:Buy_Now_Button_integration)
- [ ] [eCheck integration](http://wiki.payneteasy.com/index.php/PnE:eCheck_integration)
- [ ] [Western Union Integration](http://wiki.payneteasy.com/index.php/PnE:Western_Union_Integration)
- [ ] [Bitcoin Integration](http://wiki.payneteasy.com/index.php/PnE:Bitcoin_integration)
- [ ] [Loan Integration](http://wiki.payneteasy.com/index.php/PnE:Loan_integration)
- [ ] [Qiwi Integration](http://wiki.payneteasy.com/index.php/PnE:Qiwi_integration)
- [x] [Merchant Callbacks](http://wiki.payneteasy.com/index.php/PnE:Merchant_Callbacks)

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
