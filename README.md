# PHP Library for PaynetEasy API integration [![Build Status](https://travis-ci.org/payneteasy/php-library-payneteasy-api.png?branch=master)](https://travis-ci.org/payneteasy/php-library-paynet)
## Доступная функциональность

Данная библиотека позволяет производить оплату с помощью [merchant PaynetEasy API](http://wiki.payneteasy.com/index.php/PnE:Merchant_API). На текущий момент реализованы следующие платежные методы:
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

## Системные требования

* PHP 5.3 - 5.5
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

* [Простой пример использования библиотеки](doc/ru/00-basic-tutorial.md)
* [Внутренняя структура библиотеки](doc/ru/01-library-internals.md)
    * [Семейство классов для хранения и обмена данными, PaynetEasy\PaynetEasyApi\PaymentData](doc/ru/library-internals/00-payment-data.md)
    * [Фронтенд библиотеки, PaynetEasy\PaynetEasyApi\PaymentProcessor](doc/ru/library-internals/01-payment-processor.md)
    * [Валидатор данных, PaynetEasy\PaynetEasyApi\Util\Validator](doc/ru/library-internals/02-validator.md)
    * [Класс для работы с цепочками свойств, PaynetEasy\PaynetEasyApi\Util\PropertyAccessor](doc/ru/library-internals/03-property-accessor.md)
* [Интеграция различных платежных сценариев](doc/ru/02-payment-scenarios.md)
    * [Sale transactions](doc/ru/payment-scenarios/00-sale-transactions.md)
    * [Preauth/Capture Transactions](doc/ru/payment-scenarios/01-preauth-capture-transactions.md)
    * [Transfer Transactions](doc/ru/payment-scenarios/02-transfer-transactions.md)
    * [Return Transactions](doc/ru/payment-scenarios/03-return-transactions.md)
    * [Recurrent Transactions](doc/ru/payment-scenarios/04-recurrent-transactions.md)
    * [Payment Form Integration](doc/ru/payment-scenarios/05-payment-form-integration.md)
    * [Merchant Callbacks](doc/ru/payment-scenarios/06-merchant-callbacks.md)
