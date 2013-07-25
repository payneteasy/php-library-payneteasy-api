# Payment form integration

## Общие положения

* В данной статье описывается исключительно работа с библиотекой. Полная информация о выполнении Payment form integration расположена в [статье в wiki PaynetEasy](http://wiki.payneteasy.com/index.php/PnE:Payment_Form_integration).
* Описание правил валидации можно найти в описании метода **[Validator::validateByRule()](../library-internals/02-validator.md#validateByRule)**.
* Описание работы с цепочками свойств можно найти в описании класса **[PropertyAccessor](../library-internals/03-property-accessor.md)**

## <a name="form"></a> Запросы "sale-form", "preauth-form", "transfer-form"

* **sale-form** - применяется для оплаты с помощью кредитной карты
* **preauth-form** - применяется для блокирования части средств кредитной карты клиента. После успешного завершения этого запроса для списания средств с карты клиента необходимо выполнить запрос **[capture](01-preauth-capture-transactions.md#capture)**
* **transfer-form** - применяется для перевода средств с одного счета на другой

При выполнении запросов информация о карте вводится на стороне PaynetEasy.

##### Обязательные параметры запроса

Поле запроса        |Цепочка свойств платежа        |Правило валидации
--------------------|-------------------------------|-----------------
client_orderid      |clientPaymentId                |Validator::ID
order_desc          |description                    |Validator::LONG_STRING
amount              |amount                         |Validator::AMOUNT
currency            |currency                       |Validator::CURRENCY
address1            |billingAddress.firstLine       |Validator::MEDIUM_STRING
city                |billingAddress.city            |Validator::MEDIUM_STRING
zip_code            |billingAddress.zipCode         |Validator::ZIP_CODE
country             |billingAddress.country         |Validator::COUNTRY
phone               |billingAddress.phone           |Validator::PHONE
ipaddress           |customer.ipAddress             |Validator::IP
email               |customer.email                 |Validator::EMAIL
redirect_url        |queryConfig.redirectUrl        |Validator::URL

##### Необязательные параметры запроса

Поле запроса        |Цепочка свойств платежа        |Правило валидации
--------------------|-------------------------------|-----------------
first_name          |customer.firstName             |Validator::MEDIUM_STRING
last_name           |customer.lastName              |Validator::MEDIUM_STRING
ssn                 |customer.ssn                   |Validator::SSN
birthday            |customer.birthday              |Validator::DATE
state               |billingAddress.state           |Validator::COUNTRY
cell_phone          |billingAddress.cellPhone       |Validator::PHONE
site_url            |queryConfig.siteUrl            |Validator::URL
server_callback_url |queryConfig.callbackUrl        |Validator::URL

[Пример выполнения запроса sale-form](../../example/sale-form.php)
[Пример выполнения запроса preauth-form](../../example/preauth-form.php)
[Пример выполнения запроса transfer-form](../../example/transfer-form.php)

## <a name="form-redirect"> Обработка результата платежа после возвращения клиента с платежной формы

При возвращении пользователя с платежной формы на сервис мерчанта будут переданы данные с результатом обработки платежа. Обработка этих данных описана в [базовом примере использования библиотеки](../00-basic-tutorial.md#stage_2).

## <a name="callback"></a> Обработка обратного вызова

После завершения обработки платежа на стороне PaynetEasy, данные с результатом обработки передаются в сервис мерчанта с помощью обратного вызова. Это необходимо, чтобы платеж был обработан сервисом мерчанта независимо от того, выполнил пользователь корректно возврат с шлюза PaynetEasy или нет. Обработка этих данных совпадает с обработкой данных для [sale-form, preauth-form или transfer-form](05-payment-form-integration.md) и описана в [базовом примере использования библиотеки](../00-basic-tutorial.md#stage_2).

[Подробнее о Merchant callbacks](06-merchant-callbacks.md)
