# Account verification

Список запросов сценария:
* [Запрос "account-verification"](#account-verification)
* [Запрос "status"](#status)
* [Запрос "sync-account-verification"](#sync-account-verification)

## Общие положения

* В данной статье описывается исключительно работа с библиотекой. Полная информация о выполнении Account verification расположена в [документации PaynetEasy](http://doc.payneteasy.com/doc/account-verification.htm).
* Описание правил валидации можно найти в описании метода **[Validator::validateByRule()](../library-internals/02-validator.md#validateByRule)**.
* Описание работы с цепочками свойств можно найти в описании класса **[PropertyAccessor](../library-internals/03-property-accessor.md)**

## <a name="account-verification"></a> Запрос "account-verification"

Запрос применяется для проверки данных клиента и его кредитной карты. После выполнения данного запроса необходимо выполнить серию запросов "**status**" для обновления статуса платежа. Для этого сервис мерчанта может вывести самообновляющуюся страницу, каждая перезагрузка которой будет выполнять запрос "**status**".

[Пример самообновляющейся страницы](../../../example/common/waitPage.php)

##### Обязательные параметры запроса

Поле запроса        |Цепочка свойств платежа            |Правило валидации
--------------------|-----------------------------------|-----------------
client_orderid      |payment.clientId                   |Validator::ID
order_desc          |payment.description                |Validator::LONG_STRING
address1            |payment.billingAddress.firstLine   |Validator::MEDIUM_STRING
city                |payment.billingAddress.city        |Validator::MEDIUM_STRING
zip_code            |payment.billingAddress.zipCode     |Validator::ZIP_CODE
country             |payment.billingAddress.country     |Validator::COUNTRY
ipaddress           |payment.customer.ipAddress         |Validator::IP
email               |payment.customer.email             |Validator::EMAIL
card_printed_name   |payment.creditCard.cardPrintedName |Validator::LONG_STRING
credit_card_number  |payment.creditCard.creditCardNumber|Validator::CREDIT_CARD_NUMBER
expire_month        |payment.creditCard.expireMonth     |Validator::MONTH
expire_year         |payment.creditCard.expireYear      |Validator::YEAR
cvv2                |payment.creditCard.cvv2            |Validator::CVV2

##### Необязательные параметры запроса

Поле запроса        |Цепочка свойств платежа            |Правило валидации
--------------------|-----------------------------------|-----------------
first_name          |payment.customer.firstName         |Validator::MEDIUM_STRING
last_name           |payment.customer.lastName          |Validator::MEDIUM_STRING
ssn                 |payment.customer.ssn               |Validator::SSN
birthday            |payment.customer.birthday          |Validator::DATE
state               |payment.billingAddress.state       |Validator::COUNTRY
phone               |payment.billingAddress.phone       |Validator::PHONE
cell_phone          |payment.billingAddress.cellPhone   |Validator::PHONE
site_url            |queryConfig.siteUrl                |Validator::URL
purpose             |payment.destination                |Validator::LONG_STRING
server_callback_url |queryConfig.callbackUrl            |Validator::URL

[Пример выполнения запроса account-verification](../../../example/account-verification.php)

## <a name="status"></a> Запрос "status"

Запрос применяется для проверки статуса платежа. Обычно требуется серия таких запросов из-за того, что обработка платежа занимает некоторое время. В зависимости от статуса платежа обработка результата этого запроса может происходить несколькими путями.

##### Необходимо обновление платежа

В том случае, если статус платежа не изменился (значение поля **status** - **processing**) и нет необходимости в дополнительных шагах авторизации, то запустить проверку статуса еще раз.

##### Обработка платежа завершена

В ответе на запрос поле **status** содержит результат обработки платежа - **approved**, **filtered**, **declined**, **error**.
Также после выполнения данного запроса будут получены данные сохраненной кредитной карты и создан объект **[RecurrentCard](../library-internals/00-payment-data.md#RecurrentCard)**. Получить доступ к **RecurrentCard** можно с помощью вызова `$paymentTransaction->getPayment()->getRecurrentCardFrom()`. В объекте будут заполнены следующие данные:
* **paynetId** - данные доступны с помощью вызова `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getPaynetId()`
* **cardPrintedName** - данные доступны с помощью вызова `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getСardPrintedName()`
* **expireYear** - данные доступны с помощью вызова `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getExpireYear()`
* **expireMonth** - данные доступны с помощью вызова `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getExpireMonth()`
* **bin** - данные доступны с помощью вызова `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getBin()`
* **lastFourDigits** - данные доступны с помощью вызова `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getLastFourDigits()`
* **cardHashId** - данные доступны с помощью вызова `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getCardHashId()`
* **cardType** - данные доступны с помощью вызова `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getCardType()`

##### Обязательные параметры запроса

Поле запроса        |Цепочка свойств платежа|Правило валидации
--------------------|-----------------------|-----------------
client_orderid      |payment.clientId       |Validator::ID
orderid             |payment.paynetId       |Validator::ID
login               |queryConfig.login      |Validator::MEDIUM_STRING

[Пример выполнения запроса status](../../../example/status.php)

## <a name="callback"></a> Обработка обратного вызова

После завершения обработки платежа на стороне PaynetEasy, данные с результатом обработки передаются в сервис мерчанта с помощью обратного вызова. Это необходимо, чтобы платеж был обработан сервисом мерчанта независимо от того, выполнил пользователь корректно возврат с шлюза PaynetEasy или нет.
[Подробнее о Merchant callbacks](06-merchant-callbacks.md)

## <a name="sync-account-verification"></a> Запрос "sync-account-verification"

Запрос применяется для проверки данных клиента и его кредитной карты. После выполнения данного запроса не нужно выполнять серию запросов "**status**" для обновления статуса платежа, ответ сервера сразу содержит все необходимые данные.

##### Обязательные параметры запроса

Поле запроса        |Цепочка свойств платежа            |Правило валидации
--------------------|-----------------------------------|-----------------
client_orderid      |payment.clientId                   |Validator::ID
order_desc          |payment.description                |Validator::LONG_STRING
address1            |payment.billingAddress.firstLine   |Validator::MEDIUM_STRING
city                |payment.billingAddress.city        |Validator::MEDIUM_STRING
zip_code            |payment.billingAddress.zipCode     |Validator::ZIP_CODE
country             |payment.billingAddress.country     |Validator::COUNTRY
ipaddress           |payment.customer.ipAddress         |Validator::IP
email               |payment.customer.email             |Validator::EMAIL
card_printed_name   |payment.creditCard.cardPrintedName |Validator::LONG_STRING
credit_card_number  |payment.creditCard.creditCardNumber|Validator::CREDIT_CARD_NUMBER
expire_month        |payment.creditCard.expireMonth     |Validator::MONTH
expire_year         |payment.creditCard.expireYear      |Validator::YEAR
cvv2                |payment.creditCard.cvv2            |Validator::CVV2

##### Необязательные параметры запроса

Поле запроса        |Цепочка свойств платежа            |Правило валидации
--------------------|-----------------------------------|-----------------
first_name          |payment.customer.firstName         |Validator::MEDIUM_STRING
last_name           |payment.customer.lastName          |Validator::MEDIUM_STRING
ssn                 |payment.customer.ssn               |Validator::SSN
birthday            |payment.customer.birthday          |Validator::DATE
state               |payment.billingAddress.state       |Validator::COUNTRY
phone               |payment.billingAddress.phone       |Validator::PHONE
cell_phone          |payment.billingAddress.cellPhone   |Validator::PHONE
site_url            |queryConfig.siteUrl                |Validator::URL
purpose             |payment.destination                |Validator::LONG_STRING
server_callback_url |queryConfig.callbackUrl            |Validator::URL

[Пример выполнения запроса sync-account-verification](../../../example/sync-account-verification.php)

После выполнения данного запроса будут получены данные сохраненной кредитной карты и создан объект **[RecurrentCard](../library-internals/00-payment-data.md#RecurrentCard)**. Получить доступ к **RecurrentCard** можно с помощью вызова `$paymentTransaction->getPayment()->getRecurrentCardFrom()`. В объекте будут заполнены следующие данные:
* **paynetId** - данные доступны с помощью вызова `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getPaynetId()`
* **cardPrintedName** - данные доступны с помощью вызова `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getСardPrintedName()`
* **expireYear** - данные доступны с помощью вызова `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getExpireYear()`
* **expireMonth** - данные доступны с помощью вызова `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getExpireMonth()`
* **bin** - данные доступны с помощью вызова `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getBin()`
* **lastFourDigits** - данные доступны с помощью вызова `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getLastFourDigits()`
* **cardHashId** - данные доступны с помощью вызова `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getCardHashId()`
* **cardType** - данные доступны с помощью вызова `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getCardType()`
