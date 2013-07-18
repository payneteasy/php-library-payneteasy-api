# Payment form integration

## Общие положения

* В данной статье описывается исключительно работа с библиотекой. Полная информация о выполнении Payment form integration расположена в [статье в wiki PaynetEasy](http://wiki.payneteasy.com/index.php/PnE:Payment_Form_integration).
* Описание правил валидации можно найти в описании метода **[Validator::validateByRule()](../library-internals/02-validator.md#validateByRule)**.
* Колонка "Свойство платежа" описывает цепочку свойств методов, которые содержат необходимые данные. Например, для получения данных из свойства **description** будет выполнен код `$payment->getDescription()`, а для свойства **creditCard.cardPrintedName** - `$payment->getCreditCard()->getCardPrintedName()`

## <a name="form"></a> Запросы "sale-form", "preauth-form", "transfer-form"

##### Обязательные параметры запроса

Поле запроса        |Свойство платежа               |Правило валидации
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

##### Необязательные параметры запроса

Поле запроса        |Свойство платежа               |Правило валидации
--------------------|-------------------------------|-----------------
site_url            |siteUrl                        |Validator::URL
first_name          |customer.firstName             |Validator::MEDIUM_STRING
last_name           |customer.lastName              |Validator::MEDIUM_STRING
ssn                 |customer.ssn                   |Validator::SSN
birthday            |customer.birthday              |Validator::DATE
state               |billingAddress.state           |Validator::COUNTRY
cell_phone          |billingAddress.cellPhone       |Validator::PHONE

[Пример выполнения запроса sale-form](../../example/sale-form.php)
[Пример выполнения запроса preauth-form](../../example/preauth-form.php)
[Пример выполнения запроса transfer-form](../../example/transfer-form.php)
