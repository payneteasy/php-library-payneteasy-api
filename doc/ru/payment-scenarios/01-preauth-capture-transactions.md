# Preauth/Capture transactions

Список запросов сценария:
* [Запрос "preauth"](#preauth)
* [Запрос "capture"](#capture)
* [Запрос "status"](#status)

## Общие положения

* В данной статье описывается исключительно работа с библиотекой. Полная информация о выполнении Preauth/Capture transactions расположена в [в документации PaynetEasy](http://doc.payneteasy.com/doc/preauth-capture-transactions.htm).
* Описание правил валидации можно найти в описании метода **[Validator::validateByRule()](../library-internals/02-validator.md#validateByRule)**.
* Описание работы с цепочками свойств можно найти в описании класса **[PropertyAccessor](../library-internals/03-property-accessor.md)**

## <a name="preauth"></a> Запрос "preauth"

Запрос применяется для блокирования части средств кредитной карты клиента. При этом информация о карте вводится на стороне сервиса мерчанта и передается в запросе к PaynetEasy. После отправки данного запроса необходимо выполнить серию запросов "**status**" для обновления статуса платежа. Для этого сервис мерчанта может вывести самообновляющуюся страницу, каждая перезагрузка которой будет выполнять запрос "**status**". После успешного завершения этого запроса для списания средств с карты клиента необходимо выполнить запрос **[capture](#capture)**.

[Пример самообновляющейся страницы](../../../example/common/waitPage.php)

##### Обязательные параметры запроса

Поле запроса        |Цепочка свойств платежа            |Правило валидации
--------------------|-----------------------------------|-----------------
client_orderid      |payment.clientId                   |Validator::ID
order_desc          |payment.description                |Validator::LONG_STRING
amount              |payment.amount                     |Validator::AMOUNT
currency            |payment.currency                   |Validator::CURRENCY
address1            |payment.billingAddress.firstLine   |Validator::MEDIUM_STRING
city                |payment.billingAddress.city        |Validator::MEDIUM_STRING
zip_code            |payment.billingAddress.zipCode     |Validator::ZIP_CODE
country             |payment.billingAddress.country     |Validator::COUNTRY
phone               |payment.billingAddress.phone       |Validator::PHONE
ipaddress           |payment.customer.ipAddress         |Validator::IP
email               |payment.customer.email             |Validator::EMAIL
card_printed_name   |payment.creditCard.cardPrintedName |Validator::LONG_STRING
credit_card_number  |payment.creditCard.creditCardNumber|Validator::CREDIT_CARD_NUMBER
expire_month        |payment.creditCard.expireMonth     |Validator::MONTH
expire_year         |payment.creditCard.expireYear      |Validator::YEAR
cvv2                |payment.creditCard.cvv2            |Validator::CVV2
redirect_url        |queryConfig.redirectUrl            |Validator::URL

##### Необязательные параметры запроса

Поле запроса        |Цепочка свойств платежа            |Правило валидации
--------------------|-----------------------------------|-----------------
first_name          |payment.customer.firstName         |Validator::MEDIUM_STRING
last_name           |payment.customer.lastName          |Validator::MEDIUM_STRING
ssn                 |payment.customer.ssn               |Validator::SSN
birthday            |payment.customer.birthday          |Validator::DATE
state               |payment.billingAddress.state       |Validator::COUNTRY
cell_phone          |payment.billingAddress.cellPhone   |Validator::PHONE
destination         |payment.destination                |Validator::LONG_STRING
site_url            |queryConfig.siteUrl                |Validator::URL
server_callback_url |queryConfig.callbackUrl            |Validator::URL

[Пример выполнения запроса preauth](../../../example/preauth.php)

## <a name="capture"></a> Запрос "capture"

Запрос применяется для списания средств, ранее блокированных запросом **[preauth](#preauth)**, с карты клиента. После выполнения данного запроса необходимо выполнить серию запросов "**status**" для обновления статуса платежа. Для этого сервис мерчанта может вывести самообновляющуюся страницу, каждая перезагрузка которой будет выполнять запрос "**status**". **Внимание!** Если в библиотеку будет передан платеж, созданный ранее для выполнения запроса **[preauth](#preauth)**, то его данные будут перезаписаны данными запроса **capture**. Создайте для запроса **capture** новый платеж.

##### Обязательные параметры запроса

Поле запроса        |Цепочка свойств платежа|Правило валидации
--------------------|-----------------------|-----------------
client_orderid      |payment.clientId       |Validator::ID
orderid             |payment.paynetId       |Validator::ID
login               |queryConfig.login      |Validator::MEDIUM_STRING

[Пример выполнения запроса capture](../../../example/capture.php)

## <a name="status"></a> Запрос "status"

Запрос применяется для проверки статуса платежа. Обычно требуется серия таких запросов из-за того, что обработка платежа занимает некоторое время. В зависимости от типа авторизации клиента (необходима 3D-авторизация или нет) и статуса платежа обработка результата этого запроса может происходить несколькими путями.

##### Необходимо обновление платежа

В том случае, если статус платежа не изменился (значение поля **status** - **processing**) и нет необходимости в дополнительных шагах авторизации, то запустить проверку статуса еще раз.

##### Необходима 3D-аторизация (только при выполнении запроса **[preauth](#preauth)**)

В ответе на запрос будет передано поле **html**, содержимое которого необходимо вывести на экран браузера клиента. Содержимое поля представляет собой форму, которая переадресует пользователя для выполнения 3D-авторизации.

##### Обработка платежа завершена

В ответе на запрос поле **status** содержит результат обработки платежа - **approved**, **filtered**, **declined**, **error**

##### Обязательные параметры запроса

Поле запроса        |Цепочка свойств платежа|Правило валидации
--------------------|-----------------------|-----------------
client_orderid      |payment.clientId       |Validator::ID
orderid             |payment.paynetId       |Validator::ID
login               |queryConfig.login      |Validator::MEDIUM_STRING

[Пример выполнения запроса status](../../../example/status.php)

## <a name="3d-redirect"></a> Обработка результата платежа после 3D-авторизации (только при выполнении запроса **[preauth](#preauth)**)

Если при обработке платежа выполнялась 3D-авторизация, то при возвращении пользователя с формы авторизации на сервис мерчанта будут переданы данные с результатом обработки платежа. Обработка этих данных совпадает с обработкой данных для [sale-form, preauth-form или transfer-form](05-payment-form-integration.md) и описана в [базовом примере использования библиотеки](../00-basic-tutorial.md#stage_2).

## <a name="callback"></a> Обработка обратного вызова

После завершения обработки платежа на стороне PaynetEasy, данные с результатом обработки передаются в сервис мерчанта с помощью обратного вызова. Это необходимо, чтобы платеж был обработан сервисом мерчанта независимо от того, выполнил пользователь корректно возврат с шлюза PaynetEasy или нет.
[Подробнее о Merchant callbacks](06-merchant-callbacks.md)
