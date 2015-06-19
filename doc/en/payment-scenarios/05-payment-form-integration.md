# Payment form integration

## General provisions

* This article only describes library usage. Full information on Payment form integration may be found at [PaynetEasy wiki article](http://wiki.payneteasy.com/index.php/PnE:Payment_Form_integration).
* Validation rules description may be found in **[Validator::validateByRule()](../library-internals/02-validator.md#validateByRule)** method description.
* Property chains usage may be found in **[PropertyAccessor](../library-internals/03-property-accessor.md)** class description

## <a name="form"></a> "sale-form", "preauth-form", "transfer-form" requests

* **sale-form** - used to make a payment with a credit card
* **preauth-form** - used to block an amount on a credit card. Later it is necessary to call **[capture](01-preauth-capture-transactions.md#capture)** to actually charge the amount
* **transfer-form** - used to transfer money from one card to another

Card data is entered on PaynetEasy gateway side.

##### Mandatory request parameters

Request field       |Payment property chain             |Validation rule
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
redirect_url        |queryConfig.redirectUrl            |Validator::URL

##### Optional request parameters

Request field       |Payment property chain             |Validation rule
--------------------|-----------------------------------|-----------------
first_name          |payment.customer.firstName         |Validator::MEDIUM_STRING
last_name           |payment.customer.lastName          |Validator::MEDIUM_STRING
ssn                 |payment.customer.ssn               |Validator::SSN
birthday            |payment.customer.birthday          |Validator::DATE
state               |payment.billingAddress.state       |Validator::COUNTRY
cell_phone          |payment.billingAddress.cellPhone   |Validator::PHONE
site_url            |queryConfig.siteUrl                |Validator::URL
server_callback_url |queryConfig.callbackUrl            |Validator::URL

[sale-form request execution example](../../example/sale-form.php)
[preauth-form request execution example](../../example/preauth-form.php)
[transfer-form request execution example](../../example/transfer-form.php)

## <a name="form-redirect"></a> Payment processing after the client has returned from the payment form

When the user is redirected from the payment form, payment processing result data will be sent to merchant service with the user. Processing of that data is described in the [basic library usage example](../00-basic-tutorial.md#stage_2).

## <a name="callback"></a> Обработка обратного вызова

## <a name="callback"></a> Callback processing

After payment processing has been finished by PaynetEasy, data with processing result is sent to merchant service using a callback. This is done to allow payment to be processed by merchant service regardless of whether user was correctly redirected from PaynetEasy gateway or not. Processing of that data is the same as processing of data for [sale-form, preauth-form или transfer-form](05-payment-form-integration.md) and it is described in the [basic library usage example](../00-basic-tutorial.md#stage_2).

[More about Merchant callbacks](06-merchant-callbacks.md)
