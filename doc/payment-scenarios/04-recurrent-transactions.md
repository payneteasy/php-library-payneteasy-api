# Recurrent transactions

Список запросов сценария:
* [Запрос "create-card-ref"](#create-card-ref)
* [Запрос "get-card-info"](#get-card-info)
* [Запрос "make-rebill"](#make-rebill)
* [Запрос "status"](#status)

## Общие положения

* В данной статье описывается исключительно работа с библиотекой. Полная информация о выполнении Recurrent transactions расположена в [статье в wiki PaynetEasy](http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions).
* Описание правил валидации можно найти в описании метода **[Validator::validateByRule()](../library-internals/02-validator.md#validateByRule)**.
* Описание работы с цепочками свойств можно найти в описании класса **[PropertyAccessor](../library-internals/03-property-accessor.md)**

## <a name="create-card-ref"></a> Запрос "create-card-ref"

Перед выполнением этого запроса необходимо выполнить один из следующих сценариев для проверки данных, которые ввел клиент:
* [Sale Transactions](00-sale-transactions.md)
* [Preauth/Capture Transactions](01-preauth-capture-transactions.md)
* [Payment Form Integration](05-payment-form-integration.md)

##### Обязательные параметры запроса

Поле запроса        |Цепочка свойств платежа        |Правило валидации
--------------------|-------------------------------|-----------------
client_orderid      |clientPaymentId                |Validator::ID
orderid             |paynetPaymentId                |Validator::ID

[Пример выполнения запроса create-card-ref](../../example/create-card-ref.php)

После выполнения данного запроса будет получен id сохраненной кредитной карты и создан объект **[RecurrentCard](../library-internals/00-payment-data.md#RecurrentCard)**. Получить доступ к **RecurrentCard** можно с помощью вызова
`$payment->getRecurrentCardFrom()`, а к ее id с помощью вызова `$payment->getRecurrentCardFrom()->getCardReferenceId()`

## <a name="get-card-info"></a> Запрос "get-card-info"

##### Обязательные параметры запроса

Перед выполнением данного запроса необходимо выполнить запрос [create-card-ref](#create-card-ref).

Поле запроса        |Цепочка свойств платежа            |Правило валидации
--------------------|-----------------------------------|-----------------
cardrefid           |recurrentCardFrom.cardReferenceId  |Validator::ID

[Пример выполнения запроса get-card-info](../../example/get-card-info.php)

После выполнения данного запроса будут получены данные сохраненной кредитной карты и создан объект **[RecurrentCard](../library-internals/00-payment-data.md#RecurrentCard)**. Получить доступ к **RecurrentCard** можно с помощью вызова `$payment->getRecurrentCardFrom()`. В объекте будут заполнены следующие данные:
* **cardPrintedName** - данные доступны с помощью вызова `$payment->getRecurrentCardFrom()->getСardPrintedName()`
* **expireYear** - данные доступны с помощью вызова `$payment->getRecurrentCardFrom()->getExpireYear()`
* **expireMonth** - данные доступны с помощью вызова `$payment->getRecurrentCardFrom()->getExpireMonth()`
* **bin** - данные доступны с помощью вызова `$payment->getRecurrentCardFrom()->getBin()`
* **lastFourDigits** - данные доступны с помощью вызова `$payment->getRecurrentCardFrom()->getLastFourDigits()`

## <a name="make-rebill"></a> Запрос "make-rebill"

Перед выполнением данного запроса необходимо выполнить запрос [create-card-ref](#create-card-ref).

##### Обязательные параметры запроса

Поле запроса        |Цепочка свойств платежа            |Правило валидации
--------------------|-----------------------------------|-----------------
client_orderid      |clientPaymentId                    |Validator::ID
order_desc          |description                        |Validator::LONG_STRING
amount              |amount                             |Validator::AMOUNT
currency            |currency                           |Validator::CURRENCY
ipaddress           |customer.ipAddress                 |Validator::IP
cardrefid           |recurrentCardFrom.cardReferenceId  |Validator::ID

##### Необязательные параметры запроса

Поле запроса        |Цепочка свойств платежа            |Правило валидации
--------------------|-----------------------------------|-----------------
comment             |comment                            |Validator::MEDIUM_STRING
cvv2                |recurrentCardFrom.cvv2             |Validator::CVV2

[Пример выполнения запроса make-rebill](../../example/make-rebill.php)

## <a name="status"></a> Запрос "status"

##### Обязательные параметры запроса

Поле запроса        |Цепочка свойств платежа        |Правило валидации
--------------------|-------------------------------|-----------------
client_orderid      |clientPaymentId                |Validator::ID
orderid             |paynetPaymentId                |Validator::ID

[Пример выполнения запроса status](../../example/status.php)
