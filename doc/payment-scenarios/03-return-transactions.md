# Return transactions

Список запросов сценария:
* [Запрос "return"](#return)
* [Запрос "status"](#status)

## Общие положения

* В данной статье описывается исключительно работа с библиотекой. Полная информация о выполнении Return transactions расположена в [статье в wiki PaynetEasy](http://wiki.payneteasy.com/index.php/PnE:Return_Transactions).
* Описание правил валидации можно найти в описании метода **[Validator::validateByRule()](../library-internals/02-validator.md#validateByRule)**.
* Описание работы с цепочками свойств можно найти в описании класса **[PropertyAccessor](../library-internals/03-property-accessor.md)**

## <a name="return"></a> Запрос "return"

Перед выполнением этого запроса необходимо провести платеж, средства за который будут возвращены.

##### Обязательные параметры запроса

Поле запроса    |Цепочка свойств платежа|Правило валидации
----------------|-----------------------|-----------------
client_orderid  |clientPaymentId        |Validator::ID
orderid         |paynetPaymentId        |Validator::ID
amount          |amount                 |Validator::AMOUNT
currency        |currency               |Validator::CURRENCY
comment         |comment                |Validator::MEDIUM_STRING

[Пример выполнения запроса return](../../example/return.php)

## <a name="status"></a> Запрос "status"

##### Обязательные параметры запроса

Поле запроса    |Цепочка свойств платежа|Правило валидации
----------------|-----------------------|-----------------
client_orderid  |clientPaymentId        |Validator::ID
orderid         |paynetPaymentId        |Validator::ID

[Пример выполнения запроса status](../../example/status.php)
