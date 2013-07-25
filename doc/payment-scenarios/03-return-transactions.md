# Return transactions

Список запросов сценария:
* [Запрос "return"](#return)
* [Запрос "status"](#status)

## Общие положения

* В данной статье описывается исключительно работа с библиотекой. Полная информация о выполнении Return transactions расположена в [статье в wiki PaynetEasy](http://wiki.payneteasy.com/index.php/PnE:Return_Transactions).
* Описание правил валидации можно найти в описании метода **[Validator::validateByRule()](../library-internals/02-validator.md#validateByRule)**.
* Описание работы с цепочками свойств можно найти в описании класса **[PropertyAccessor](../library-internals/03-property-accessor.md)**

## <a name="return"></a> Запрос "return"

Запрос применяется для возврата средств на счет клиента.
Перед выполнением этого запроса необходимо провести платеж, средства за который будут возвращены.
После выполнения данного запроса необходимо выполнить серию запросов "**status**" для обновления статуса платежа. Для этого сервис мерчанта может вывести самообновляющуюся страницу, каждая перезагрузка которой будет выполнять запрос "**status**".

##### Обязательные параметры запроса

Поле запроса    |Цепочка свойств платежа|Правило валидации
----------------|-----------------------|-----------------
client_orderid  |clientPaymentId        |Validator::ID
orderid         |paynetPaymentId        |Validator::ID
amount          |amount                 |Validator::AMOUNT
currency        |currency               |Validator::CURRENCY
comment         |comment                |Validator::MEDIUM_STRING
login           |queryConfig.login      |Validator::MEDIUM_STRING

[Пример выполнения запроса return](../../example/return.php)

## <a name="status"></a> Запрос "status"

Запрос применяется для проверки статуса платежа. Обычно требуется серия таких запросов из-за того, что обработка платежа занимает некоторое время. В зависимости от статуса платежа обработка результата этого запроса может происходить несколькими путями.

##### Необходимо обновление платежа

В том случае, если статус платежа не изменился (значение поля **status** - **processing**) и нет необходимости в дополнительных шагах авторизации, то запустить проверку статуса еще раз.

##### Обработка платежа завершена

В ответе на запрос поле **status** содержит результат обработки платежа - **approved**, **filtered**, **declined**, **error**

##### Обязательные параметры запроса

Поле запроса    |Цепочка свойств платежа|Правило валидации
----------------|-----------------------|-----------------
client_orderid  |clientPaymentId        |Validator::ID
orderid         |paynetPaymentId        |Validator::ID
login           |queryConfig.login      |Validator::MEDIUM_STRING

[Пример выполнения запроса status](../../example/status.php)
