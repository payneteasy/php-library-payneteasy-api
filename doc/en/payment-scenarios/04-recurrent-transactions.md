# Recurrent transactions

Scenario requests list:
* ["create-card-ref" request](#create-card-ref)
* ["get-card-info" request](#get-card-info)
* ["make-rebill" request](#make-rebill)
* ["status" request](#status)

## General provisions

* This article only describes library usage. Full information on Recurrent transactions processing may be found at [PaynetEasy wiki article](http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions).
* Validation rules description may be found in **[Validator::validateByRule()](../library-internals/02-validator.md#validateByRule)** method description.
* Property chains usage may be found in **[PropertyAccessor](../library-internals/03-property-accessor.md)** class description

## <a name="create-card-ref"></a> "create-card-ref" query

This request is used to create an ID which may be used to reference a card stored at PaynetEasy. This ID allows to make recurrent payments without a necessity to enter card data again.
For this request to be used, it is necessary to perform one of the following scenarios to create verification transaction:
* [Sale Transactions](00-sale-transactions.md)
* [Preauth/Capture Transactions](01-preauth-capture-transactions.md)
* [Payment Form Integration](05-payment-form-integration.md)

##### Mandatory request parameters

Request field       |Payment property chain         |Validation rule
--------------------|-------------------------------|-----------------
client_orderid      |payment.clientId               |Validator::ID
orderid             |payment.paynetId               |Validator::ID
login               |queryConfig.login              |Validator::MEDIUM_STRING

[create-card-ref request execution example](../../../example/create-card-ref.php)

This request creates an ID of a saved credit card embedded in **[RecurrentCard](../library-internals/00-payment-data.md#RecurrentCard)** object. To access **RecurrentCard**, call `$paymentTransaction->getPayment()->getRecurrentCardFrom()`; to access card reference ID, call `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getCardReferenceId()`

## <a name="get-card-info"></a> "get-card-info" request

Used to get information about a saved credit card.
For this to work, a [create-card-ref](#create-card-ref) must be executed before.

##### Mandatory request parameters

Request field       |Payment property chain             |Validation rule
--------------------|-----------------------------------|-----------------
cardrefid           |payment.recurrentCardFrom.paynetId |Validator::ID
login               |queryConfig.login                  |Validator::MEDIUM_STRING

[get-card-info request execution](../../../example/get-card-info.php)

When this request is executed, an instance of **[RecurrentCard](../library-internals/00-payment-data.md#RecurrentCard)** is created. To access the **RecurrentCard**, you can use `$paymentTransaction->getPayment()->getRecurrentCardFrom()`. Following data will be filled in the object:
* **cardPrintedName** - available via `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getСardPrintedName()`
* **expireYear** - available via `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getExpireYear()`
* **expireMonth** - available via `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getExpireMonth()`
* **bin** - available via `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getBin()`
* **lastFourDigits** - available via `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getLastFourDigits()`

## <a name="make-rebill"></a> "make-rebill" request

Used to charge saved client's credit card.
The card must be saved using [create-card-ref](#create-card-ref).
After executing this request, "**status**" request needs to be polled to update payment status. To achieve this, merchant service may display a self-updating page, each reload of which will make "**status**" request.

##### Mandatory request parameters

Request field       |Payment property chain             |Validation rule
--------------------|-----------------------------------|-----------------
client_orderid      |payment.clientId                   |Validator::ID
order_desc          |payment.description                |Validator::LONG_STRING
amount              |payment.amount                     |Validator::AMOUNT
currency            |payment.currency                   |Validator::CURRENCY
ipaddress           |payment.customer.ipAddress         |Validator::IP
cardrefid           |payment.recurrentCardFrom.paynetId |Validator::ID
login               |queryConfig.login                  |Validator::MEDIUM_STRING

##### Optional request parameters

Request field       |Payment property chain         |Validation rule
--------------------|-------------------------------|-----------------
comment             |payment.comment                |Validator::MEDIUM_STRING
cvv2                |payment.recurrentCardFrom.cvv2 |Validator::CVV2
server_callback_url |queryConfig.callbackUrl        |Validator::URL

[make-rebill request execution example](../../../example/make-rebill.php)

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

## <a name="callback"></a> Callback processing

After payment processing has been finished by PaynetEasy, data with processing result is sent to merchant service using a callback. This is done to allow payment to be processed by merchant service regardless of whether user was correctly redirected from PaynetEasy gateway or not.
[More about Merchant callbacks](06-merchant-callbacks.md)
