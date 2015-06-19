# Library frontend, PaymentProcessor

Library frontend is represented with **[PaynetEasy\PaynetEasyApi\PaymentProcessor](../../../source/PaynetEasy/PaynetEasyApi/PaymentProcessor.php)** class. Class provides the following methods:
* **[executeQuery()](#executeQuery)**: execute PaynetEasy request
* **[processCustomerReturn()](#processCustomerReturn)**: process data received from PaynetEasy when user was returned from the payment gateway
* **[processPaynetEasyCallback()](#processPaynetEasyCallback)**: process data received from PaynetEasy in a callback
* **[setHandlers()](#setHandlers)**: set handlers for various events which occur while processing the payment transaction

### <a name="executeQuery"></a>executeQuery(): execute PaynetEasy request

Some payment processing scenarios have asynchronous nature and consist of several requests. For instance, certain requests do not return payment result right away and require to poll **status**, after which client may be redirected to PaynetEasy gateway to perform additional authorization steps. After client has returned to merchant service, processing of data received from the gateway is necessary.
<a name="async_queries_list"></a>Asynchronous requests list:
* sale
* preauth
* capture
* return
* make-rebill
* transfer-by-ref

Following files contain examples of processing of such requests:
* [Example of sale request execution](../../../example/sale.php)
* [Example of preauth request execution](../../../example/preauth.php)
* [Example of capture request execution](../../../example/capture.php)
* [Example of return request execution](../../../example/return.php)
* [Example of make-rebill request execution](../../../example/make-rebill.php)
* [Example of transfer-by-ref request execution](../../../example/transfer-by-ref.php)

A processing scenario is required for payment form integration as well. Response from a gateway returns a URL pointing to payment form to which the client must be redirected. After filling and submission of data gateway processes the payment form and returns the client to merchant service. After the client has been returned to merchant service, processing of data received from the gateway is required.
<a name="form_queries_list"></a>List of requests for payment form integration:
* sale-form
* preauth-form
* transfer-form

Following files contain examples of processing of such requests:
* [Example of sale-form request execution](../../../example/sale-form.php)
* [Example of preauth-form request execution](../../../example/preauth-form.php)
* [Example of transfer-form request execution](../../../example/transfer-form.php)

Some payment operations do not require complex processing scenarios and are performed in one request.
List of simple payment operations:
* create-card-ref
* get-card-info
* status

Following files contain examples of processing of such requests:
* [Example of create-card-ref request execution](../../../example/create-card-ref.php)
* [Example of get-card-info request execution](../../../example/get-card-info.php)
* [Example of status request execution](../../../example/status.php)

For convenient PaynetEasy request execution, **[executeQuery()](../../../source/PaynetEasy/PaynetEasyApi/PaymentProcessor.php#L114)** method is implemented in **PaymentProcessor**.
Method accepts two parameters:
* Request name
* Payment transaction for processing

### <a name="processCustomerReturn"></a>processCustomerReturn(): processing of the data received from PaynetEasy when returning the client

Every [asynchronous request](#async_queries_list) may finish with customer redirect to the payment gateway to fulfill additional actions, and every [payment form integration request](#form_queries_list) necessarily contains such a redirection. Every time, when a customer is returned, processing result data is transmitted to merchant service. Also, if [initial request configuration](../00-basic-tutorial.md#stage_1_step_3) has **server_callback_url** key defined, then after payment processing has been finished, PaynetEasy will call that URL and send to it data described in PaynetEasy wiki in [Merchant Callbacks](http://wiki.payneteasy.com/index.php/PnE:Merchant_Callbacks) section. For convenient processing of that data, **PaymentProcessor** has **[processCustomerReturn()](../../../source/PaynetEasy/PaynetEasyApi/PaymentProcessor.php#L144)** method.
Method accepts two parameters:
* Object with data received when customer has returned from PaynetEasy via redirect
* Payment transaction for processing

Following files contain examples of processing of such requests:
* [Basic library usage example](../00-basic-tutorial.md#stage_2)
* [Example of sale request execution](../../../example/sale.php#L96)
* [Example of preauth request execution](../../../example/preauth.php#L96)
* [Example of sale-form request execution](../../../example/sale-form.php#L75)
* [Example of preauth-form request execution](../../../example/preauth-form.php#L75)
* [Example of transfer-form request execution](../../../example/transfer-form.php#L75)

### <a name="processPaynetEasyCallback"></a>processPaynetEasyCallback(): processing PaynetEasy callback

After an [asynchronous request](#async_queries_list) or [payment form integration request](#form_queries_list) has been executed, if an [initial request configuration](../00-basic-tutorial.md#stage_1_step_3) contains **server_callback_url** key defined, then after some time PaynetEasy will call that URL and send to it data described in PaynetEasy wiki in section [Merchant Callbacks](http://wiki.payneteasy.com/index.php/PnE:Merchant_Callbacks). For convenient processing of that data **PaymentProcessor** has **[processPaynetEasyCallback()](../../../source/PaynetEasy/PaynetEasyApi/PaymentProcessor.php#L159)** method.
Method accepts two parameters:
* Object with data received when customer was redirected from PaynetEasy
* Payment transaction for processing

Following files contain examples of processing of such requests:
* [Example of sale request execution](../../../example/sale.php#L107)
* [Example of preauth request execution](../../../example/preauth.php#L107)
* [Example of sale-form request execution](../../../example/sale-form.php#L86)
* [Example of preauth-form request execution](../../../example/preauth-form.php#L86)
* [Example of transfer-form request execution](../../../example/transfer-form.php#L86)

### <a name="setHandlers"></a> setHandlers(): setting handlers for various events occurring during order processing

**PaymentProcessor** hides the order processing algorithm from the end user in **[executeQuery()](#executeQuery)**, **[processCustomerReturn()](#processCustomerReturn)** and **[processPaynetEasyCallback()](#processPaynetEasyCallback)** methods. During order processing situations arise, which must be handled at merchant service side. To handle such situations, **PaymentProcessor** has event handling system. Handlers may be installed using three ways:
* Pass handlers array to [**PaymentProcessor** class constructor](../../../source/PaynetEasy/PaynetEasyApi/PaymentProcessor.php#L101)
* Pass handlers array to [**setHandlers()**](../../../source/PaynetEasy/PaynetEasyApi/PaymentProcessor.php#L250) method
* Install handlers one by one using **[setHandler()](../../../source/PaynetEasy/PaynetEasyApi/PaymentProcessor.php#L226)** method

Event handlers list:
* **HANDLER_SAVE_CHANGES** - handler to save payment transaction. It is called when payment transaction data is changed. It must save the payment transaction to a storage. Accepts the following parameters:
    * Payment transaction
    * PaynetEasy response (optional; not available, if an error at early stage of request execution has occurred)
* **HANDLER_STATUS_UPDATE** - handler to update payment transaction status. It is called, if payment transaction status did not changed since the last check. It must initiate payment transaction status check. Accepts the following parameters:
    * PaynetEasy response
    * Payment transaction
* **HANDLER_SHOW_HTML** - handler to output HTML code received from PaynetEasy. Called when user 3D Authorization is required. Must output HTML code from PaynetEasy response to client's browser. Accepts the following parameters:
    * PaynetEasy response
    * Payment transaction
* **HANDLER_REDIRECT** - handler to redirect a client to PaynetEasy payment form. Called after executing [sale-form, preauth-form or transfer-form](../payment-scenarios/05-payment-form-integration.md). Must redirect a user to URL from PaynetEasy response. Accepts the following parameters:
    * PaynetEasy response
    * Payment transaction
* **HANDLER_FINISH_PROCESSING** - handler to proceed payment transaction processing after it has been processed by the library. Called if there is no need of further transaction processing. Accepts the following parameters:
    * Payment transaction
    * PaynetEasy response (optional; not available, if payment transaction processing has already been finished earlier)
* **HANDLER_CATCH_EXCEPTION** - exception handler. Called if an exception occurres while processing. **Warning!** If this handler is not installed, then the exception will be thrown from the library to merchant service code. Accepts the following parameters:
    * Exception
    * Payment transaction
    * PaynetEasy response (optional; not available, if an error at early stage of request execution has occurred)

Method accepts one parameter:
* Event handlers array. Handler names (defined in class constants) are array keys. Values are any values of [callable](http://php.net/manual/en/language.types.callable.php) type

An example of method invocation with simple handlers:

```php
use PaynetEasy\PaynetEasyApi\PaymentProcessor;
use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\Transport\Response;
use Exception;

$paymentProcessor = new PaymentProcessor;
$paymentProcessor->setHandlers(array
(
    PaymentProcessor::HANDLER_SAVE_CHANGES      => function(PaymentTransaction $paymentTransaction)
    {
        start_session();
        $_SESSION['payment_transaction'] = serialize($paymentTransaction);
    },
    PaymentProcessor::HANDLER_STATUS_UPDATE     => function()
    {
        header("Location: http://{$_SERVER['HTTP_HOST']}{$_SERVER['SCRIPT_NAME']}?stage=updateStatus");
        exit;
    },
    PaymentProcessor::HANDLER_SHOW_HTML         => function(Response $response)
    {
        print $response->getHtml();
        exit;
    },
    PaymentProcessor::HANDLER_REDIRECT          => function(Response $response)
    {
        header("Location: {$response->getRedirectUrl()}");
        exit;
    },
    PaymentProcessor::HANDLER_FINISH_PROCESSING => function(PaymentTransaction $paymentTransaction)
    {
        print "<pre>";
        print "Payment processing finished.\n";
        print "Payment status: '{$paymentTransaction->getPayment()->getStatus()}'.\n";
        print "Payment transaction status: '{$paymentTransaction->getStatus()}'.\n";
        print "</pre>";
    },
    PaymentProcessor::HANDLER_CATCH_EXCEPTION   => function(Exception $exception)
    {
        print "<pre>";
        print "Exception catched.\n";
        print "Exception message: '{$exception->getMessage()}'.\n";
        print "Exception traceback: \n{$exception->getTraceAsString()}\n";
        print "</pre>";
    }
));
```
