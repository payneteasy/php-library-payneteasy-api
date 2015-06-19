# Preauth/Capture transactions

Scenario requests list:
* ["preauth" request](#preauth)
* ["capture" request](#capture)
* ["status" request](#status)

## General provisions

* This article only describes library usage. Full information on Sale transactions processing may be found at [PaynetEasy wiki article](http://wiki.payneteasy.com/index.php/PnE:Preauth/Capture_Transactions).
* Validation rules description may be found in **[Validator::validateByRule()](../library-internals/02-validator.md#validateByRule)** method description.
* Property chains usage may be found in **[PropertyAccessor](../library-internals/03-property-accessor.md)** class description

## <a name="preauth"></a> "preauth" request

This request is used to block an amount on client's credit card. Card data is entered at merchant service side and sent to PaynetEasy. After executing this request, "**status**" request needs to be polled to update payment status. To achieve this, merchant service may display a self-updating page, each reload of which will make "**status**" request. After a successful processing of this request, to charge client's card you need to execute **[capture](#capture)** request.

[Self-updating page example](../../example/common/waitPage.html)

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
card_printed_name   |payment.creditCard.cardPrintedName |Validator::LONG_STRING
credit_card_number  |payment.creditCard.creditCardNumber|Validator::CREDIT_CARD_NUMBER
expire_month        |payment.creditCard.expireMonth     |Validator::MONTH
expire_year         |payment.creditCard.expireYear      |Validator::YEAR
cvv2                |payment.creditCard.cvv2            |Validator::CVV2
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
destination         |payment.destination                |Validator::LONG_STRING
site_url            |queryConfig.siteUrl                |Validator::URL
server_callback_url |queryConfig.callbackUrl            |Validator::URL

[preauth request execution example](../../example/preauth.php)

## <a name="capture"></a> "capture" request

This request is used to charge client's card for amount which was earlier blocked with **[preauth](#preauth)** request. After executing this request, "**status**" request needs to be polled to update payment status. To achieve this, merchant service may display a self-updating page, each reload of which will make "**status**" request. **Warning!** If payment is passed to the library which was created earlier to execute **[preauth](#preauth)** request, its data will be overwritten with **capture** data. Create a new payment for **capture** request.

##### Mandatory request parameters

Request field       |Payment property chain |Validation rule
--------------------|-----------------------|-----------------
client_orderid      |payment.clientId       |Validator::ID
orderid             |payment.paynetId       |Validator::ID
login               |queryConfig.login      |Validator::MEDIUM_STRING

[capture request execution example](../../example/capture.php)

## <a name="status"></a> "status" request

This request is used to check payment status. Usually, a series of such requests is required, because payment processingn takes some time. Depending on client authorization type (is 3D Authorization required or not) and payment status, processing of this request may be performed in different ways.

##### Payment update is required

If payment status did not change (**status** field has **processing** value) and there is not need of additional authorization steps, status needs to be called again after some time.

##### 3D Authorization is required

Response will contain **html** field, which content needs to be output to client's browser. Field content is a form which redirects a user to perform 3D Authorization.

##### Payment processing finished

Response's **status** field contains final payment status: **approved**, **filtered**, **declined**, **error**, **unknown**

##### Mandatory request parameters

Request parameter   |Payment property chain |Validation chain
--------------------|-----------------------|-----------------
client_orderid      |payment.clientId       |Validator::ID
orderid             |payment.paynetId       |Validator::ID
login               |queryConfig.login      |Validator::MEDIUM_STRING

[status request execution example](../../example/status.php)

## <a name="3d-redirect"></a> Processing payment result after 3D Authorization (only after **[preauth](#preauth)** request execution)

If a 3D Authorization was performed during payment processing, then when a user is redirected back from authorization form, payment processing result data will be sent to merchant service. Processing of this data is the same as processing for [sale-form, preauth-form или transfer-form](05-payment-form-integration.md) flows, it is described in the [basic library usage example](../00-basic-tutorial.md#stage_2).

## <a name="callback"></a> Callback processing

After payment processing has been finished by PaynetEasy, data with processing result is sent to merchant service using a callback. This is done to allow payment to be processed by merchant service regardless of whether user was correctly redirected from PaynetEasy gateway or not.
[More about Merchant callbacks](06-merchant-callbacks.md)
