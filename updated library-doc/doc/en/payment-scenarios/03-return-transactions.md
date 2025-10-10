# Return transactions

Scenario requests list:
* ["return" request](#return)
* ["status" request](#status)

## General provisions

* This article only describes library usage. Full information on Return transactions processing may be found at [PaynetEasy documentation section](http://doc.payneteasy.com/doc/return-transactions.htm).
* Validation rules description may be found in **[Validator::validateByRule()](../library-internals/02-validator.md#validateByRule)** method description.
* Property chains usage may be found in **[PropertyAccessor](../library-internals/03-property-accessor.md)** class description

## <a name="return"></a> "return" request

This request is used to return money charged by a previous payment request made through PaynetEasy; money is returned to the client's card.
After executing this request, "**status**" request needs to be polled to update payment status. To achieve this, merchant service may display a self-updating page, each reload of which will make "**status**" request.

##### Mandatory request parameters

Request field   |Payment property chain |Validation rule
----------------|-----------------------|-----------------
client_orderid  |payment.clientId       |Validator::ID
orderid         |payment.paynetId       |Validator::ID
amount          |payment.amount         |Validator::AMOUNT
currency        |payment.currency       |Validator::CURRENCY
comment         |payment.comment        |Validator::MEDIUM_STRING
login           |queryConfig.login      |Validator::MEDIUM_STRING

[Пример выполнения запроса return](../../../example/return.php)

## <a name="status"></a> "status" request

This request is used to check payment status. Usually, a series of such requests is required, because payment processingn takes some time. Depending on client authorization type (is 3D Authorization required or not) and payment status, processing of this request may be performed in different ways.

##### Payment update is required

If payment status did not change (**status** field has **processing** value) and there is not need of additional authorization steps, status needs to be called again after some time.

##### Payment processing finished

Response's **status** field contains final payment status: **approved**, **filtered**, **declined**, **error**, **unknown**

##### Mandatory request parameters

Request parameter   |Payment property chain |Validation chain
--------------------|-----------------------|-----------------
client_orderid      |payment.clientId       |Validator::ID
orderid             |payment.paynetId       |Validator::ID
login               |queryConfig.login      |Validator::MEDIUM_STRING

[status request execution example](../../../example/status.php)
