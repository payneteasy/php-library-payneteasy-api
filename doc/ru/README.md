# PHP Library for PaynetEasy API integration [![Build Status](https://travis-ci.org/payneteasy/php-library-payneteasy-api.png?branch=master)](https://travis-ci.org/payneteasy/php-library-paynet)
## Доступная функциональность

Данная библиотека позволяет производить оплату с помощью [PaynetEasy Merchant API](http://doc.payneteasy.com/). На текущий момент реализованы следующие платежные методы:
- [x] [Account verification](http://doc.payneteasy.com/doc/account-verification.htm)
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

## Системные требования

* PHP 5.4 - 5.6
* [Расширение curl](http://php.net/manual/en/book.curl.php)

## Установка

1. [Установите composer](http://getcomposer.org/doc/00-intro.md), если его еще нет
2. Перейдите в папку проекта: `cd my/project/directory`
3. Создайте файл проекта для composer, если его еще нет: `composer init`
4. Добавьте библиотеку в зависимости проекта: `composer require payneteasy/php-library-payneteasy-api:dev-master --prefer-dist`

## Запуск тестов

1. Перейдите в папку с библиотекой: `cd vendor/payneteasy/php-library-payneteasy-api/`
2. Запустите тесты: `phpunit -c test/phpunit.xml test`

## Использование

* [Простой пример использования библиотеки](00-basic-tutorial.md)
* [Внутренняя структура библиотеки](01-library-internals.md)
    * [Семейство классов для хранения и обмена данными, PaynetEasy\PaynetEasyApi\PaymentData](library-internals/00-payment-data.md)
    * [Фронтенд библиотеки, PaynetEasy\PaynetEasyApi\PaymentProcessor](library-internals/01-payment-processor.md)
    * [Валидатор данных, PaynetEasy\PaynetEasyApi\Util\Validator](library-internals/02-validator.md)
    * [Класс для работы с цепочками свойств, PaynetEasy\PaynetEasyApi\Util\PropertyAccessor](library-internals/03-property-accessor.md)
* [Интеграция различных платежных сценариев](02-payment-scenarios.md)
    * [Account verification](payment-scenarios/07-account-verification.md)
    * [Sale transactions](payment-scenarios/00-sale-transactions.md)
    * [Preauth/Capture Transactions](payment-scenarios/01-preauth-capture-transactions.md)
    * [Transfer Transactions](payment-scenarios/02-transfer-transactions.md)
    * [Return Transactions](payment-scenarios/03-return-transactions.md)
    * [Recurrent Transactions](payment-scenarios/04-recurrent-transactions.md)
    * [Payment Form Integration](payment-scenarios/05-payment-form-integration.md)
    * [Merchant Callbacks](payment-scenarios/06-merchant-callbacks.md)
