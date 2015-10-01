# Account verification

Scenario requests list:
* ["account-verification" request](#account-verification)
* ["status" request](#status)
* ["sync-account-verification" request](#sync-account-verification)

## General provisions

* This article only describes library usage. Full information on Account verification may be found at [PaynetEasy documentation section](http://doc.payneteasy.com/doc/account-verification.htm).
* Validation rules description may be found in **[Validator::validateByRule()](../library-internals/02-validator.md#validateByRule)**.
* Property chains usage may be found in **[PropertyAccessor](../library-internals/03-property-accessor.md)**

## <a name="account-verification"></a> "account-verification" request

This request is used to validate customer personal data and his credit card date. After executing this request, "**status**" request needs to be polled to update payment status. To achieve this, merchant service may display a self-updating page, each reload of which will make "**status**" request.

[Self-updating page example](../../../example/common/waitPage.php)

##### Mandatory request parameters

Request field       |Payment property chain             |Validation rule
--------------------|-----------------------------------|-----------------
client_orderid      |payment.clientId                   |Validator::ID
order_desc          |payment.description                |Validator::LONG_STRING
address1            |payment.billingAddress.firstLine   |Validator::MEDIUM_STRING
city                |payment.billingAddress.city        |Validator::MEDIUM_STRING
zip_code            |payment.billingAddress.zipCode     |Validator::ZIP_CODE
country             |payment.billingAddress.country     |Validator::COUNTRY
ipaddress           |payment.customer.ipAddress         |Validator::IP
email               |payment.customer.email             |Validator::EMAIL
card_printed_name   |payment.creditCard.cardPrintedName |Validator::LONG_STRING
credit_card_number  |payment.creditCard.creditCardNumber|Validator::CREDIT_CARD_NUMBER
expire_month        |payment.creditCard.expireMonth     |Validator::MONTH
expire_year         |payment.creditCard.expireYear      |Validator::YEAR
cvv2                |payment.creditCard.cvv2            |Validator::CVV2

##### Optional request parameters

Request field       |Payment property chain             |Validation rule
--------------------|-----------------------------------|-----------------
first_name          |payment.customer.firstName         |Validator::MEDIUM_STRING
last_name           |payment.customer.lastName          |Validator::MEDIUM_STRING
ssn                 |payment.customer.ssn               |Validator::SSN
birthday            |payment.customer.birthday          |Validator::DATE
state               |payment.billingAddress.state       |Validator::COUNTRY
phone               |payment.billingAddress.phone       |Validator::PHONE
cell_phone          |payment.billingAddress.cellPhone   |Validator::PHONE
site_url            |queryConfig.siteUrl                |Validator::URL
purpose             |payment.destination                |Validator::LONG_STRING
server_callback_url |queryConfig.callbackUrl            |Validator::URL

[account-verification request execution example](../../../example/account-verification.php)

## <a name="status"></a> "status" request

This request is used to check payment status. Usually, a series of such requests is required, because payment processingn takes some time. Depending on client authorization type (is 3D Authorization required or not) and payment status, processing of this request may be performed in different ways.

##### Payment update is required

If payment status did not change (**status** field has **processing** value) and there is not need of additional authorization steps, status needs to be called again after some time.

##### Payment processing finished

Response's **status** field contains final payment status: **approved**, **filtered**, **declined**, **error**, **unknown**
Also, when this request is executed, an instance of **[RecurrentCard](../library-internals/00-payment-data.md#RecurrentCard)** is created. To access the **RecurrentCard**, you can use `$paymentTransaction->getPayment()->getRecurrentCardFrom()`. Following data will be filled in the object:
* **paynetId** - available via `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getPaynetId()`
* **cardPrintedName** - available via `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getСardPrintedName()`
* **expireYear** - available via `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getExpireYear()`
* **expireMonth** - available via `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getExpireMonth()`
* **bin** - available via `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getBin()`
* **lastFourDigits** - available via `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getLastFourDigits()`
* **cardHashId** - available via `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getCardHashId()`
* **cardType** - available via `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getCardType()`

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

## <a name="sync-account-verification"></a> "sync-account-verification" request

This request is used to validate customer personal data and his credit card date. After executing this request, you don't have to execute "**status**" request, because server response has all necessary data.

##### Mandatory request parameters

Request field       |Payment property chain             |Validation rule
--------------------|-----------------------------------|-----------------
client_orderid      |payment.clientId                   |Validator::ID
order_desc          |payment.description                |Validator::LONG_STRING
address1            |payment.billingAddress.firstLine   |Validator::MEDIUM_STRING
city                |payment.billingAddress.city        |Validator::MEDIUM_STRING
zip_code            |payment.billingAddress.zipCode     |Validator::ZIP_CODE
country             |payment.billingAddress.country     |Validator::COUNTRY
ipaddress           |payment.customer.ipAddress         |Validator::IP
email               |payment.customer.email             |Validator::EMAIL
card_printed_name   |payment.creditCard.cardPrintedName |Validator::LONG_STRING
credit_card_number  |payment.creditCard.creditCardNumber|Validator::CREDIT_CARD_NUMBER
expire_month        |payment.creditCard.expireMonth     |Validator::MONTH
expire_year         |payment.creditCard.expireYear      |Validator::YEAR
cvv2                |payment.creditCard.cvv2            |Validator::CVV2

##### Optional request parameters

Request field       |Payment property chain             |Validation rule
--------------------|-----------------------------------|-----------------
first_name          |payment.customer.firstName         |Validator::MEDIUM_STRING
last_name           |payment.customer.lastName          |Validator::MEDIUM_STRING
ssn                 |payment.customer.ssn               |Validator::SSN
birthday            |payment.customer.birthday          |Validator::DATE
state               |payment.billingAddress.state       |Validator::COUNTRY
phone               |payment.billingAddress.phone       |Validator::PHONE
cell_phone          |payment.billingAddress.cellPhone   |Validator::PHONE
site_url            |queryConfig.siteUrl                |Validator::URL
purpose             |payment.destination                |Validator::LONG_STRING
server_callback_url |queryConfig.callbackUrl            |Validator::URL

[sync-account-verification request execution example](../../../example/sync-account-verification.php)

When this request is executed, an instance of **[RecurrentCard](../library-internals/00-payment-data.md#RecurrentCard)** is created. To access the **RecurrentCard**, you can use `$paymentTransaction->getPayment()->getRecurrentCardFrom()`. Following data will be filled in the object:
* **paynetId** - available via `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getPaynetId()`
* **cardPrintedName** - available via `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getСardPrintedName()`
* **expireYear** - available via `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getExpireYear()`
* **expireMonth** - available via `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getExpireMonth()`
* **bin** - available via `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getBin()`
* **lastFourDigits** - available via `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getLastFourDigits()`
* **cardHashId** - available via `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getCardHashId()`
* **cardType** - available via `$paymentTransaction->getPayment()->getRecurrentCardFrom()->getCardType()`