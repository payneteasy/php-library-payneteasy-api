# Классы для хранения и передачи данных

Семейство классов для хранения данных и обмена данными между библиотекой и CMS мерчанта. Расположены в пространстве имен **[PaynetEasy\PaynetEasyApi\PaymentData](../../../source/PaynetEasy/PaynetEasyApi/PaymentData)**. Представлены следующими классами объектов:
* [PaymentTransaction](#PaymentTransaction)
* [Payment](#Payment)
* [QueryConfig](#QueryConfig)
* [Customer](#Customer)
* [BillingAddress](#BillingAddress)
* [CreditCard](#CreditCard)
* [RecurrentCard](#RecurrentCard)

Каждый из классов позволяет наполнять объект данными как с помощью массива, переданного в конструктор, так и с помощью сеттеров. При использовании массива в качестве ключа для данных необходимо использовать underscored название свойства класса.

##### Использование массива и underscored названий свойств класса

```php
$payment = new Payment(array
(
    'client_id'         => 'CLIENT-112233',
    'paynet_id'         => 'PAYNET-112233',
    'description'       => 'test payment'
));
```
##### Использование сеттеров

```php
$payment = (new Payment)
    ->setClientId('CLIENT-112233')
    ->setPaynetId('PAYNET-112233')
    ->setDescription('test payment')
;
```

### <a name="PaymentTransaction"></a> PaymentTransaction

Центральным объектом для хранения и передачи данных является объект класса **[PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction](../../source/PaynetEasy/PaynetEasyApi/PaymentData/PaymentTransaction.php)**. Именно он передается из CMS в библиотеку при выполнении любого запроса. Хранит следующие данные:

Свойство класса     |Тип                        |Поле запроса   |Назначение
--------------------|---------------------------|---------------|-------------------------------------------------------
processorType       |string                     |               |Transaction processor type (query, callback)
processorName       |string                     |               |Transaction processor name (query or callback name)
status              |string                     |               |Transaction status (new, processing, approved, filtered, declined, error)
payment             |[Payment](#Payment)        |               |Платеж, для которого создана транзакция
queryConfig         |[QueryConfig](#QueryConfig)|               |Payment query config
errors              |array                      |               |Transaction processing errors

Поля **processorType** и **processorName** заполняются обработчиком транзакции после формирования платежного запроса. Поле **status** изменяется на основе данных ответа от сервера PaynetEasy.

### <a name="Payment"></a> Payment

Объект класса **[PaynetEasy\PaynetEasyApi\PaymentData\Payment](../../source/PaynetEasy/PaynetEasyApi/PaymentData/Payment.php)**. Используется при выполнении всех запросов. Хранит следующие данные:

Свойство класса     |Тип                                |Поле запроса   |Назначение
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

Поле **status** заполняется обработчиком транзакции после формирования платежного запроса в зависимости от того, какую платежную операцию реализует обработчик.

### <a name="QueryConfig"></a> QueryConfig

Объект класса **[PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig](../../source/PaynetEasy/PaynetEasyApi/PaymentData/QueryConfig.php)**. Используется при выполнении всех запросов. Хранит следующие данные:

Свойство класса     |Тип    |Поле запроса       |Назначение
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

Значение свойства **endPoint** участвует в формировании URL для вызова платежного метода шлюза PaynetEasy, а свойства **signingKey** - в формировании подписи для данных платежа. Значения свойств **gatewayUrlSandbox** и **gatewayUrlProduction** содержат ссылки на sandbox и production гейты. Выбор между этими url осуществляется на основе значения поля **gatewayMode**, если значение поля `QueryConfig::GATEWAY_MODE_SANDBOX`, то будет выбран url **gatewayUrlSandbox**, если `QueryConfig::GATEWAY_MODE_PRODUCTION` - то url **gatewayUrlProduction**.

### <a name="Customer"></a> Customer

Объект класса **[PaynetEasy\PaynetEasyApi\PaymentData\Customer](../../source/PaynetEasy/PaynetEasyApi/PaymentData/Customer.php)**. Используется при выполнении следующих запросов:
* [sale](../payment-scenarios/00-sale-transactions.md#sale)
* [preauth](../payment-scenarios/01-preauth-capture-transactions.md#preauth)
* [sale-form, preauth-form, transfer-form](../payment-scenarios/05-payment-form-integration.md#form)
* [make-rebill](../payment-scenarios/04-recurrent-transactions.md#make-rebill)
* [transfer-by-ref](../payment-scenarios/02-transfer-transactions.md#transfer-by-ref)

Объект хранит следующие данные:

Свойство класса     |Тип    |Поле запроса   |Назначение
--------------------|-------|---------------|-------------------------------------------------------
firstName           |string |first_name     |Customer’s first name
lastName            |string |last_name      |Customer’s last name
email               |string |email          |Customer’s email address
ipAddress           |string |ipaddress      |Customer’s IP address
birthday            |string |birthday       |Customer’s date of birth, in the format MMDDYY
ssn                 |string |ssn            |Last four digits of the customer’s social security number

### <a name="BillingAddress"></a> BillingAddress

Объект класса **[PaynetEasy\PaynetEasyApi\PaymentData\BillingAddress](../../source/PaynetEasy/PaynetEasyApi/PaymentData/BillingAddress.php)**. Используется при выполнении следующих запросов:
* [sale](../payment-scenarios/00-sale-transactions.md#sale)
* [preauth](../payment-scenarios/01-preauth-capture-transactions.md#preauth)
* [sale-form, preauth-form, transfer-form](../payment-scenarios/05-payment-form-integration.md#form)

Объект хранит следующие данные:

Свойство класса     |Тип    |Поле запроса   |Назначение
--------------------|-------|---------------|-------------------------------------------------------
country             |string |country        |Customer’s two-letter country code
state               |string |state          |Customer’s two-letter state code
city                |string |city           |Customer’s city
firstLine           |string |address1       |Customer’s address line 1
zipCode             |string |zip_code       |Customer’s ZIP code
phone               |string |phone          |Customer’s full international phone number, including country code
cellPhone           |string |cell_phone     |Customer’s full international cell phone number, including country code

### <a name="CreditCard"></a> CreditCard

Объект класса **[PaynetEasy\PaynetEasyApi\PaymentData\CreditCard](../../source/PaynetEasy/PaynetEasyApi/PaymentData/CreditCard.php)**. Используется при выполнении следующих запросов:
* [sale](../payment-scenarios/00-sale-transactions.md#sale)
* [preauth](../payment-scenarios/01-preauth-capture-transactions.md#preauth)

Объект хранит следующие данные:

Свойство класса     |Тип    |Поле запроса       |Назначение
--------------------|-------|-------------------|-------------------------------------------------------
cvv2                |integer|cvv2               |RecurrentCard CVV2
cardPrintedName     |string |card_printed_name  |Card holder name
creditCardNumber    |string |credit_card_number |Credit card number
expireYear          |integer|expire_year        |Card expiration year
expireMonth         |integer|expire_month       |Card expiration month

### <a name="RecurrentCard"></a> RecurrentCard

Объект класса **[PaynetEasy\PaynetEasyApi\PaymentData\RecurrentCard](../../source/PaynetEasy/PaynetEasyApi/PaymentData/RecurrentCard.php)**. Используется при выполнении следующих запросов:
* [create-card-ref](../payment-scenarios/04-recurrent-transactions.md#create-card-ref)
* [get-card-info](../payment-scenarios/04-recurrent-transactions.md#get-card-info)
* [make-rebill](../payment-scenarios/04-recurrent-transactions.md#make-rebill)
* [transfer-by-ref](../payment-scenarios/02-transfer-transactions.md#transfer-by-ref)

Объект хранит следующие данные:

Свойство класса     |Тип    |Поле запроса       |Назначение
--------------------|-------|-------------------|-------------------------------------------------------
paynetId            |integer|cardrefid          |RecurrentCard PaynetEasy ID
cvv2                |integer|cvv2               |RecurrentCard CVV2
cardPrintedName     |string |                   |Card holder name
expireYear          |integer|                   |Card expiration year
expireMonth         |integer|                   |Card expiration month
bin                 |integer|                   |Bank Identification Number
lastFourDigits      |integer|                   |The last four digits of PAN (card number)
