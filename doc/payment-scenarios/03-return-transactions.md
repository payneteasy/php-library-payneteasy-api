# Return transactions

## Общие положения

* В данной статье описывается исключительно работа с библиотекой. Полная информация о выполнении Return transactions расположена в [статье в wiki PaynetEasy](http://wiki.payneteasy.com/index.php/PnE:Return_Transactions).
* Описание правил валидации можно найти в описании метода **[Validator::validateByRule()](../library-internals/02-validator.md#validateByRule)**.
* Колонка "Свойство платежа" описывает цепочку свойств методов, которые содержат необходимые данные. Например, для получения данных из свойства **description** будет выполнен код `$payment->getDescription()`, а для свойства **creditCard.cardPrintedName** - `$payment->getCreditCard()->getCardPrintedName()`

## Запрос "return"

Перед выполнением этого запроса необходимо провести платеж, средства за который будут возвращены.

##### Обязательные параметры запроса

Поле запроса    |Свойство платежа   |Правило валидации
----------------|-------------------|-----------------
client_orderid  |clientPaymentId    |Validator::ID
orderid         |paynetPaymentId    |Validator::ID
amount          |amount             |Validator::AMOUNT
currency        |currency           |Validator::CURRENCY
comment         |comment            |Validator::MEDIUM_STRING

[Пример выполнения запроса return](../../example/return.php)

## Запрос "status"

##### Обязательные параметры запроса

Поле запроса    |Свойство платежа   |Правило валидации
----------------|-------------------|-----------------
client_orderid  |clientPaymentId    |Validator::ID
orderid         |paynetPaymentId    |Validator::ID

[Пример выполнения запроса status](../../example/status.php)
