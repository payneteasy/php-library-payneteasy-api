# PHP Library for PaynetEasy API integration [![Build Status](https://travis-ci.org/payneteasy/php-library-payneteasy-api.png?branch=master)](https://travis-ci.org/payneteasy/php-library-paynet)
## Доступная функциональность

Данная библиотека позволяет производить оплату с помощью [PaynetEasy Merchant API](http://doc.payneteasy.com/). На текущий момент реализованы следующие платежные методы:
- [x] [Account Verification](https://doc.payneteasy.eu/integration/ru/api_use_cases/server_to_server_account_verification.html)
- [x] [Sale Transactions](https://doc.payneteasy.eu/integration/ru/api_use_cases/server_to_server_sale.html)
- [x] [Preauth/Capture Transactions](https://doc.payneteasy.eu/integration/ru/api_use_cases/server_to_server_preauth_capture_and_cancel.html)
- [x] [Transfer Transactions](https://doc.payneteasy.eu/integration/ru/api_use_cases/server_to_server_transfer.html)
- [x] [Return Transactions](https://doc.payneteasy.eu/integration/ru/api_use_cases/return_transaction.html)
- [x] [Recurrent Transactions](https://doc.payneteasy.eu/integration/ru/api_use_cases/recurring_sale.html)
- [x] [Payment Form Integration](https://doc.payneteasy.eu/integration/ru/api_use_cases/sale_form.html)
- [x] [Connecting Party Callbacks](https://doc.payneteasy.eu/integration/ru/API_commands/merchant_callback_parameters.html)

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
