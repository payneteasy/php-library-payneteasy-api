# Data storage and exchange classes

Classes for data storage and exchange between the library and merchant CMS. Located in namespace **[PaynetEasy\PaynetEasyApi\PaymentData](../../../source/PaynetEasy/PaynetEasyApi/PaymentData)**. Represented with the following classes:
* [PaymentTransaction](#PaymentTransaction)
* [Payment](#Payment)
* [QueryConfig](#QueryConfig)
* [Customer](#Customer)
* [BillingAddress](#BillingAddress)
* [CreditCard](#CreditCard)
* [RecurrentCard](#RecurrentCard)

Each of the classes allows to fill object with data using either an array passed to a constructor, or with setters. When using an array, it is necessary to use underscored class property names as data keys.

##### Using an array and underscored class property names

```php
$payment = new Payment(array
(
    'client_id'         => 'CLIENT-112233',
    'paynet_id'         => 'PAYNET-112233',
    'description'       => 'test payment'
));
```
##### Using setters

```php
$payment = (new Payment)
    ->setClientId('CLIENT-112233')
    ->setPaynetId('PAYNET-112233')
    ->setDescription('test payment')
;
```

### <a name="PaymentTransaction"></a> PaymentTransaction

Central object for data storage and exchange is instance of  **[PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction](../../../source/PaynetEasy/PaynetEasyApi/PaymentData/PaymentTransaction.php)** class. This is the object that is passed from CMS to the library when executing any request. It has the following data:

Class property      |Type                       |Request field  |Purpose
--------------------|---------------------------|---------------|-------------------------------------------------------
processorType       |string                     |               |Transaction processor type (query, callback)
processorName       |string                     |               |Transaction processor name (query or callback name)
status              |string                     |               |Transaction status (new, processing, approved, filtered, declined, error)
payment             |[Payment](#Payment)        |               |Payment for which a transaction is created
queryConfig         |[QueryConfig](#QueryConfig)|               |Payment query config
errors              |array                      |               |Transaction processing errors

**processorType** and **processorName** fields are filled by transaction handler after building the payment request. **status** field is changed according to data of the response from PaynetEasy server.

### <a name="Payment"></a> Payment

Instance of **[PaynetEasy\PaynetEasyApi\PaymentData\Payment](../../../source/PaynetEasy/PaynetEasyApi/PaymentData/Payment.php)** class. Used when executing any request. It has the following data:

Class property      |Type                               |Request field  |Purpose
--------------------|-----------------------------------|---------------|-------------------------------------------------------
clientId            |string                             |client_orderid |Merchant payment identifier
paynetId            |string                             |orderid        |Unique identifier of transaction assigned by PaynetEasy
description         |string                             |order_desc     |Brief payment description
destination         |string                             |destination    |Destination to where the payment goes
amount              |float                              |amount         |Amount to be charged
currency            |string                             |currency       |Three-letter currency code
comment             |string                             |comment        |A short comment for payment
status              |string                             |               |Payment status (new, preauth, capture, return)
customer            |[Customer](#Customer)              |               |Payment customer
billingAddress      |[BillingAddress](#BillingAddress)  |               |Payment billing address
creditCard          |[CreditCard](#CreditCard)          |               |Payment credit card
recurrentCardFrom   |[RecurrentCard](#RecurrentCard)    |               |Payment source recurrent card
recurrentCardTo     |[RecurrentCard](#RecurrentCard)    |               |Payment destination recurrent card

The **status** field is filled by transaction handler after building payment request according to operation implemented by the handler.

### <a name="QueryConfig"></a> QueryConfig

Instance of **[PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig](../../../source/PaynetEasy/PaynetEasyApi/PaymentData/QueryConfig.php)** class. Used when executing any request. It has the following data:

Class property      |Type   |Request field      |Purpose
--------------------|-------|-------------------|-------------------------------------------------------
login               |string |login              |Merchant login
siteUrl             |string |site_url           |URL the original payment is made from
redirectUrl         |string |redirect_url       |URL the customer will be redirected to upon completion of the transaction
callbackUrl         |string |server_callback_url|URL the transaction result will be sent to
endPoint            |integer|                   |Merchant end point
signingKey          |string |                   |Merchant key for payment signing
gatewayMode         |string |                   |Gateway mode (sandbox, production)
gatewayUrlSandbox   |string |                   |Sandbox gateway url
gatewayUrlProduction|string |                   |Production gateway url

**endPoint** property value takes part in building of URL to call PaynetEasy gateway payment method, and **signingKey** is used to create a signature of payment data. **gatewayUrlSandbox** and **gatewayUrlProduction** property values are URLs of sandbox and production gateways. Selection of one of these URLs is made according to **gatewayMode**: if field value is `QueryConfig::GATEWAY_MODE_SANDBOX`, then **gatewayUrlSandbox** is chosen, and if it is `QueryConfig::GATEWAY_MODE_PRODUCTION`, then **gatewayUrlProduction** is used.

### <a name="Customer"></a> Customer

Instance of **[PaynetEasy\PaynetEasyApi\PaymentData\Customer](../../../source/PaynetEasy/PaynetEasyApi/PaymentData/Customer.php)** class. Used when executing the following requests:
* [sale](../payment-scenarios/00-sale-transactions.md#sale)
* [preauth](../payment-scenarios/01-preauth-capture-transactions.md#preauth)
* [sale-form, preauth-form, transfer-form](../payment-scenarios/05-payment-form-integration.md#form)
* [make-rebill](../payment-scenarios/04-recurrent-transactions.md#make-rebill)
* [transfer-by-ref](../payment-scenarios/02-transfer-transactions.md#transfer-by-ref)

Object has the following data:

Class property      |Type   |Request field  |Purpose
--------------------|-------|---------------|-------------------------------------------------------
firstName           |string |first_name     |Customer’s first name
lastName            |string |last_name      |Customer’s last name
email               |string |email          |Customer’s email address
ipAddress           |string |ipaddress      |Customer’s IP address
birthday            |string |birthday       |Customer’s date of birth, in the format MMDDYY
ssn                 |string |ssn            |Last four digits of the customer’s social security number

### <a name="BillingAddress"></a> BillingAddress

Instance of **[PaynetEasy\PaynetEasyApi\PaymentData\BillingAddress](../../../source/PaynetEasy/PaynetEasyApi/PaymentData/BillingAddress.php)** class. Used when executing the following requests:
* [sale](../payment-scenarios/00-sale-transactions.md#sale)
* [preauth](../payment-scenarios/01-preauth-capture-transactions.md#preauth)
* [sale-form, preauth-form, transfer-form](../payment-scenarios/05-payment-form-integration.md#form)

Object has the following data:

Class property      |Type   |Request field  |Purpose
--------------------|-------|---------------|-------------------------------------------------------
country             |string |country        |Customer’s two-letter country code
state               |string |state          |Customer’s two-letter state code
city                |string |city           |Customer’s city
firstLine           |string |address1       |Customer’s address line 1
zipCode             |string |zip_code       |Customer’s ZIP code
phone               |string |phone          |Customer’s full international phone number, including country code
cellPhone           |string |cell_phone     |Customer’s full international cell phone number, including country code

### <a name="CreditCard"></a> CreditCard

Instance of **[PaynetEasy\PaynetEasyApi\PaymentData\CreditCard](../../../source/PaynetEasy/PaynetEasyApi/PaymentData/CreditCard.php)** class. Used when executing the following requests:
* [sale](../payment-scenarios/00-sale-transactions.md#sale)
* [preauth](../payment-scenarios/01-preauth-capture-transactions.md#preauth)

Object has the following data:

Class property      |Type   |Request field      |Purpose
--------------------|-------|-------------------|-------------------------------------------------------
cvv2                |integer|cvv2               |RecurrentCard CVV2
cardPrintedName     |string |card_printed_name  |Card holder name
creditCardNumber    |string |credit_card_number |Credit card number
expireYear          |integer|expire_year        |Card expiration year
expireMonth         |integer|expire_month       |Card expiration month

### <a name="RecurrentCard"></a> RecurrentCard

Instance of **[PaynetEasy\PaynetEasyApi\PaymentData\RecurrentCard](../../../source/PaynetEasy/PaynetEasyApi/PaymentData/RecurrentCard.php)** class. Used when executing the following requests:
* [create-card-ref](../payment-scenarios/04-recurrent-transactions.md#create-card-ref)
* [get-card-info](../payment-scenarios/04-recurrent-transactions.md#get-card-info)
* [make-rebill](../payment-scenarios/04-recurrent-transactions.md#make-rebill)
* [transfer-by-ref](../payment-scenarios/02-transfer-transactions.md#transfer-by-ref)

Object has the following data:

Class property      |Type   |Request field      |Purpose
--------------------|-------|-------------------|-------------------------------------------------------
paynetId            |integer|cardrefid          |RecurrentCard PaynetEasy ID
cvv2                |integer|cvv2               |RecurrentCard CVV2
cardPrintedName     |string |                   |Card holder name
expireYear          |integer|                   |Card expiration year
expireMonth         |integer|                   |Card expiration month
bin                 |integer|                   |Bank Identification Number
lastFourDigits      |integer|                   |The last four digits of PAN (card number)
