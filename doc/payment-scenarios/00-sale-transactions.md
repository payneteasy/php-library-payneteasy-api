# Sale transactions

Список запросов сценария:
* [Запрос "sale"](#sale)
* [Запрос "status"](#status)

## Общие положения

* В данной статье описывается исключительно работа с библиотекой. Полная информация о выполнении Sale transactions расположена в [статье в wiki PaynetEasy](http://wiki.payneteasy.com/index.php/PnE:Sale_Transactions).
* Описание правил валидации можно найти в описании метода **[Validator::validateByRule()](../library-internals/02-validator.md#validateByRule)**.
* Описание работы с цепочками свойств можно найти в описании класса **[PropertyAccessor](../library-internals/03-property-accessor.md)**

## <a name="sale"></a> Запрос "sale"

Запрос применяется для оплаты с помощью кредитной карты. При этом информация о карте вводится на стороне сервиса мерчанта и передается в запросе к PaynetEasy. После выполнения данного запроса необходимо выполнить серию запросов "**status**" для обновления статуса платежа. Для этого сервис мерчанта может вывести самообновляющуюся страницу, каждая перезагрузка которой будет выполнять запрос "**status**".

[Пример самообновляющейся страницы](../../example/common/waitPage.html)

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
card_printed_name   |creditCard.cardPrintedName     |Validator::LONG_STRING
credit_card_number  |creditCard.creditCardNumber    |Validator::CREDIT_CARD_NUMBER
expire_month        |creditCard.expireMonth         |Validator::MONTH
expire_year         |creditCard.expireYear          |Validator::YEAR
cvv2                |creditCard.cvv2                |Validator::CVV2
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
destination         |destination                    |Validator::LONG_STRING
site_url            |queryConfig.siteUrl            |Validator::URL
server_callback_url |queryConfig.callbackUrl        |Validator::URL

[Пример выполнения запроса sale](../../example/sale.php)

## <a name="status"></a> Запрос "status"

Запрос применяется для проверки статуса платежа. Обычно требуется серия таких запросов из-за того, что обработка платежа занимает некоторое время. В зависимости от типа авторизации клиента (необходима 3D-авторизация или нет) и статуса платежа обработка результата этого запроса может происходить несколькими путями.

##### Необходимо обновление платежа

В том случае, если статус платежа не изменился (значение поля **status** - **processing**) и нет необходимости в дополнительных шагах авторизации, то запустить проверку статуса еще раз.

##### Необходима 3D-аторизация

В ответе на запрос будет передано поле **html**, содержимое которого необходимо вывести на экран браузера клиента. Содержимое поля представляет собой форму, которая переадресует пользователя для выполнения 3D-авторизации.

##### Обработка платежа завершена

В ответе на запрос поле **status** содержит результат обработки платежа - **approved**, **filtered**, **declined**, **error**

##### Обязательные параметры запроса

Поле запроса        |Цепочка свойств платежа        |Правило валидации
--------------------|-------------------------------|-----------------
client_orderid      |clientPaymentId                |Validator::ID
orderid             |paynetPaymentId                |Validator::ID
login               |queryConfig.login              |Validator::MEDIUM_STRING

[Пример выполнения запроса status](../../example/status.php)

## <a name="3d-redirect"> Обработка результата платежа после 3D-авторизации

Если при обработке платежа выполнялась 3D-авторизация, то при возвращении пользователя с формы авторизации на сервис мерчанта будут переданы данные с результатом обработки платежа. Обработка этих данных совпадает с обработкой данных для [sale-form, preauth-form или transfer-form](05-payment-form-integration.md) и описана в [базовом примере использования библиотеки](../00-basic-tutorial.md#stage_2).

## <a name="callback"></a> Обработка обратного вызова

После завершения обработки платежа на стороне PaynetEasy, данные с результатом обработки передаются в сервис мерчанта с помощью обратного вызова. Это необходимо, чтобы платеж был обработан сервисом мерчанта независимо от того, выполнил пользователь корректно возврат с шлюза PaynetEasy или нет. Обработка этих данных совпадает с обработкой данных для [sale-form, preauth-form или transfer-form](05-payment-form-integration.md) и описана в [базовом примере использования библиотеки](../00-basic-tutorial.md#stage_2).

[Подробнее о Merchant callbacks](06-merchant-callbacks.md)
