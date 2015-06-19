# Recurrent transactions

Список запросов сценария:
* [Запрос "create-card-ref"](#create-card-ref)
* [Запрос "get-card-info"](#get-card-info)
* [Запрос "make-rebill"](#make-rebill)
* [Запрос "status"](#status)

## Общие положения

* В данной статье описывается исключительно работа с библиотекой. Полная информация о выполнении Recurrent transactions расположена в [в документации PaynetEasy](http://doc.payneteasy.com/doc/recurrent-transactions.htm).
* Описание правил валидации можно найти в описании метода **[Validator::validateByRule()](../library-internals/02-validator.md#validateByRule)**.
* Описание работы с цепочками свойств можно найти в описании класса **[PropertyAccessor](../library-internals/03-property-accessor.md)**

## <a name="create-card-ref"></a> Запрос "create-card-ref"

Запрос применяется для получения id для кредитной карты, сохраненной на стороне PaynetEasy. Этот id позволяет совершать повторные платежи без ввода данных кредитной карты как на стороне сервиса мерчанта так и на стороне PaynetEasy.
Перед выполнением этого запроса необходимо выполнить один из следующих сценариев для проверки данных, которые ввел клиент:
* [Sale Transactions](00-sale-transactions.md)
* [Preauth/Capture Transactions](01-preauth-capture-transactions.md)
* [Payment Form Integration](05-payment-form-integration.md)

##### Обязательные параметры запроса

Поле запроса        |Цепочка свойств платежа        |Правило валидации
--------------------|-------------------------------|-----------------
client_orderid      |payment.clientId               |Validator::ID
orderid             |payment.paynetId               |Validator::ID
login               |queryConfig.login              |Validator::MEDIUM_STRING

[Пример выполнения запроса create-card-ref](../../../example/create-card-ref.php)

После выполнения данного запроса будет получен id сохраненной кредитной карты и создан объект **[RecurrentCard](../library-internals/00-payment-data.md#RecurrentCard)**. Получить доступ к **RecurrentCard** можно с помощью вызова `$paymentTransaction->getPayment()->getRecurrentCardFrom()`, а к ее id с помощью вызова `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getCardReferenceId()`

## <a name="get-card-info"></a> Запрос "get-card-info"

Запрос применяется для получения некоторых данных сохраненной кредитной карты.
Перед выполнением данного запроса необходимо выполнить запрос [create-card-ref](#create-card-ref).

##### Обязательные параметры запроса

Поле запроса        |Цепочка свойств платежа            |Правило валидации
--------------------|-----------------------------------|-----------------
cardrefid           |payment.recurrentCardFrom.paynetId |Validator::ID
login               |queryConfig.login                  |Validator::MEDIUM_STRING

[Пример выполнения запроса get-card-info](../../../example/get-card-info.php)

После выполнения данного запроса будут получены данные сохраненной кредитной карты и создан объект **[RecurrentCard](../library-internals/00-payment-data.md#RecurrentCard)**. Получить доступ к **RecurrentCard** можно с помощью вызова `$paymentTransaction->getPayment()->getRecurrentCardFrom()`. В объекте будут заполнены следующие данные:
* **cardPrintedName** - данные доступны с помощью вызова `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getСardPrintedName()`
* **expireYear** - данные доступны с помощью вызова `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getExpireYear()`
* **expireMonth** - данные доступны с помощью вызова `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getExpireMonth()`
* **bin** - данные доступны с помощью вызова `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getBin()`
* **lastFourDigits** - данные доступны с помощью вызова `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getLastFourDigits()`

## <a name="make-rebill"></a> Запрос "make-rebill"

Запрос применяется для списания средств с кредитной карты клиента.
Перед выполнением данного запроса необходимо выполнить запрос [create-card-ref](#create-card-ref).
После выполнения данного запроса необходимо выполнить серию запросов "**status**" для обновления статуса платежа. Для этого сервис мерчанта может вывести самообновляющуюся страницу, каждая перезагрузка которой будет выполнять запрос "**status**".

##### Обязательные параметры запроса

Поле запроса        |Цепочка свойств платежа            |Правило валидации
--------------------|-----------------------------------|-----------------
client_orderid      |payment.clientId                   |Validator::ID
order_desc          |payment.description                |Validator::LONG_STRING
amount              |payment.amount                     |Validator::AMOUNT
currency            |payment.currency                   |Validator::CURRENCY
ipaddress           |payment.customer.ipAddress         |Validator::IP
cardrefid           |payment.recurrentCardFrom.paynetId |Validator::ID
login               |queryConfig.login                  |Validator::MEDIUM_STRING

##### Необязательные параметры запроса

Поле запроса        |Цепочка свойств платежа        |Правило валидации
--------------------|-------------------------------|-----------------
comment             |payment.comment                |Validator::MEDIUM_STRING
cvv2                |payment.recurrentCardFrom.cvv2 |Validator::CVV2
server_callback_url |queryConfig.callbackUrl        |Validator::URL

[Пример выполнения запроса make-rebill](../../../example/make-rebill.php)

## <a name="status"></a> Запрос "status"

Запрос применяется для проверки статуса платежа. Обычно требуется серия таких запросов из-за того, что обработка платежа занимает некоторое время. В зависимости от статуса платежа обработка результата этого запроса может происходить несколькими путями.

##### Необходимо обновление платежа

В том случае, если статус платежа не изменился (значение поля **status** - **processing**) и нет необходимости в дополнительных шагах авторизации, то запустить проверку статуса еще раз.

##### Обработка платежа завершена

В ответе на запрос поле **status** содержит результат обработки платежа - **approved**, **filtered**, **declined**, **error**

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
