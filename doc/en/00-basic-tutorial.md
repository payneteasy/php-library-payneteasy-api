# Simple library usage example

Let's walk through requests execution in [payment form integration](http://wiki.payneteasy.com/index.php/PnE:Payment_Form_integration). Typical payment processing is made in three stages. First and last stages take place on Merchant service side, while second stage is on PaynetEasy side.

1. Payment initiation:
    1. [Include classloader and required classes](#stage_1_step_1)
    2. [Create new payment transaction](#stage_1_step_2)
    3. [Create a service for payment processing](#stage_1_step_4)
    4. [Launch payment transaction processing](#stage_1_step_6)
        1. Check payment transaction data and build a request to PaynetEasy from that data
        3. Change payment status **status**
        4. Execute a request to start payment transaction initial validation and processing
        5. Receive a response from PaynetEasy
        6. Change payment transaction status **status** according to response data
        7. Save payment transaction
        8. Redirect the client to payment form форму

2. Payment form processing:
    1. Client fills in the payment form and submits it to PaynetEasy
    2. Data is being processed by PaynetEasy
    3. Client is being redirected to Merchant service along with payment form processing result

3. Result processing:
    1. [Include classloader and required classes](#stage_2_step_1)
    2. [Load saved payment transaction](#stage_2_step_2)
    3. [Create a service for payment processing](#stage_2_step_4)
    4. [Launch processing of the data received with client when client was returned from the payment form](#stage_2_step_6)
        1. Validate data received with client when client was returned from the payment form
        2. Change payment transaction status **status**
        3. Save payment transaction
        4. Output payment status **status** and payment transaction status **status** for user

Here are examples of code that accomplish both stages. Code that performs second stage should execute when user visits URL defined in the settings under **redirect_url** key. For instance, place first stage source code in file `first_stage.php`, and code for the second stage in `second_stage.php`.

### <a name="stage_1"></a>Start of payment transaction processing

1. <a name="stage_1_step_1"></a>Include class loader [provided by Composer](http://getcomposer.org/doc/01-basic-usage.md#autoloading) and required classes:

    ```php
    require_once 'project/root/dir/vendor/autoload.php';

    use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
    use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
    use PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig;
    use PaynetEasy\PaynetEasyApi\PaymentData\BillingAddress;
    use PaynetEasy\PaynetEasyApi\PaymentData\Customer;
    use PaynetEasy\PaynetEasyApi\Transport\Response;
    use PaynetEasy\PaynetEasyApi\PaymentProcessor;
    use Exception;
    ```
2. <a name="stage_1_step_2"></a>Create new payment transaction:
    ##### Using arrays passed to constructor:

    ```php
    $customer = new Customer(array
    (
        'email'                     => 'vass.pupkin@example.com',
        'ip_address'                => '127.0.0.1'
    ));

    $billingAddress = new BillingAddress(array
    (
        'country'                   => 'US',
        'city'                      => 'Houston',
        'state'                     => 'TX',
        'first_line'                => '2704 Colonial Drive',
        'zip_code'                  => '1235',
        'phone'                     => '660-485-6353'
    ));

    $queryConfig = new QueryConfig(array
    (
        'end_point'                 =>  253,
        'login'                     => 'rp-merchant1',
        'signing_key'               => '3FD4E71A-D84E-411D-A613-40A0FB9DED3A',
        'redirect_url'              => "http://{$_SERVER['HTTP_HOST']}/second_stage.php",
        'gateway_mode'              =>  QueryConfig::GATEWAY_MODE_SANDBOX,
        'gateway_url_sandbox'       => 'https://sandbox.domain.com/paynet/api/v2/',
        'gateway_url_production'    => 'https://payment.domain.com/paynet/api/v2/'
    ));

    $payment = new Payment(array
    (
        'client_id'                 => 'CLIENT-112244',
        'description'               => 'This is test payment',
        'amount'                    =>  9.99,
        'currency'                  => 'USD',
        'customer'                  => $customer,
        'billing_address'           => $billingAddress
    ));

    $paymentTransaction = new PaymentTransaction(array
    (
        'payment'                   => $payment,
        'query_config'              => $queryConfig
    ));
    ```
    ##### Using setters:

    ```php
    $customer = (new Customer)
        ->setEmail('vass.pupkin@example.com')
        ->setIpAddress('127.0.0.1')
    ;

    $billingAddress = (new BillingAddress)
        ->setCountry('US')
        ->setState('TX')
        ->setCity('Houston')
        ->setFirstLine('2704 Colonial Drive')
        ->setZipCode('1235')
        ->setPhone('660-485-6353')
    ;

    $queryConfig = (new QueryConfig)
        ->setEndPoint(253)
        ->setLogin('rp-merchant1')
        ->setSigningKey('3FD4E71A-D84E-411D-A613-40A0FB9DED3A')
        ->setRedirectUrl("http://{$_SERVER['HTTP_HOST']}/second_stage.php")
        ->setGatewayMode(QueryConfig::GATEWAY_MODE_SANDBOX)
        ->setGatewayUrlSandbox('https://sandbox.domain.com/paynet/api/v2/')
        ->setGatewayUrlProduction('https://payment.domain.com/paynet/api/v2/')
    ;

    $payment = (new Payment)
        ->setClientId('CLIENT-112244')
        ->setDescription('This is test payment')
        ->setAmount(9.99)
        ->setCurrency('USD')
        ->setCustomer($customer)
        ->setBillingAddress($billingAddress)
    ;

    $paymentTransaction = (new PaymentTransaction)
        ->setPayment($payment)
        ->setQueryConfig($queryConfig)
    ;
    ```

    Request configuration fields **QueryConfig**:
    * **[end_point](http://wiki.payneteasy.com/index.php/PnE:Introduction#Endpoint)** - end point for merchant account, created when registering
    * **[login](http://wiki.payneteasy.com/index.php/PnE:Introduction#PaynetEasy_Users)** - merchant login to access PaynetEasy UI, created when registering
    * **signing_key** - merchant control key for request signing, created when registering
    * **[redirect_url](http://wiki.payneteasy.com/index.php/PnE:Payment_Form_integration#Payment_Form_final_redirect)** - URL to which user will be redirected after payment processing has finished
    * **gateway_mode** - library mode: sandbox, production
    * **gateway_url_sandbox** - URL of PaynetEasy sandbox processing gateway
    * **gateway_url_production** - URL of PaynetEasy production processing gateway

3. <a name="stage_1_step_4"></a>Create payment processing service:
    ```php
    $paymentProcessor = new PaymentProcessor(array
    (
        PaymentProcessor::HANDLER_CATCH_EXCEPTION   => function(Exception $exception)
        {
            print "<pre>";
            print "Exception catched.\n";
            print "Exception message: '{$exception->getMessage()}'.\n";
            print "Exception traceback: \n{$exception->getTraceAsString()}\n";
            print "</pre>";
        },
        PaymentProcessor::HANDLER_SAVE_CHANGES      => function(PaymentTransaction $paymentTransaction)
        {
            start_session();
            $_SESSION['payment_transaction'] = serialize($paymentTransaction);
        },
        PaymentProcessor::HANDLER_REDIRECT          => function(Response $response)
        {
            header("Location: {$response->getRedirectUrl()}");
            exit;
        }
    ));
    ```

    Service event handlers:
    * **PaymentProcessor::HANDLER_CATCH_EXCEPTION** - to handle exception if it is thrown
    * **PaymentProcessor::HANDLER_SAVE_CHANGES** - to save payment transaction
    * **PaymentProcessor::HANDLER_REDIRECT** - to redirect user to payment form URL received from PaynetEasy

4. <a name="stage_1_step_6"></a>Start payment processing:

    ```php
    $paymentProcessor->executeQuery('sale-form', $paymentTransaction);
    ```
    Following steps will be performed:
    1. Validate payment transaction data and build a request to PaynetEasy from it
    3. Change payment status **status**
    4. Execute request to start payment transaction initial validation and processing
    5. Receive response from PaynetEasy
    6. Change payment transaction status **status** according to response data
    7. Save payment transaction with event handler for `PaymentProcessor::HANDLER_SAVE_PAYMENT`
    8. Redirect client to payment form with event handler for `PaymentProcessor::HANDLER_REDIRECT`

### <a name="stage_2"></a>Finish transaction payment processing

1. <a name="stage_2_step_1"></a>Include class loader [provided by Composer](http://getcomposer.org/doc/01-basic-usage.md#autoloading) and required classes:

    ```php
    require_once 'project/root/dir/vendor/autoload.php';

    use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
    use PaynetEasy\PaynetEasyApi\Transport\CallbackResponse;
    use PaynetEasy\PaynetEasyApi\PaymentProcessor;
    use Exception;
    ```
2. <a name="stage_2_step_2"></a>Load saved payment transaction:

    ```php
    session_start();
    $paymentTransaction = unserialize($_SESSION['payment_transaction']);
    ```

3. <a name="stage_2_step_4"></a>Create payment processing service:

    ```php
    $paymentProcessor = new PaymentProcessor(array
    (
        PaymentProcessor::HANDLER_CATCH_EXCEPTION   => function(Exception $exception)
        {
            print "<pre>";
            print "Exception catched.\n";
            print "Exception message: '{$exception->getMessage()}'.\n";
            print "Exception traceback: \n{$exception->getTraceAsString()}\n";
            print "</pre>";
        },
        PaymentProcessor::HANDLER_SAVE_CHANGES      => function(PaymentTransaction $paymentTransaction)
        {
            $_SESSION['payment_transaction'] = serialize($paymentTransaction);
        },
        PaymentProcessor::HANDLER_FINISH_PROCESSING => function(PaymentTransaction $paymentTransaction)
        {
            print "<pre>";
            print "Payment processing finished.\n";
            print "Payment status: '{$paymentTransaction->getPayment()->getStatus()}'.\n";
            print "Payment transaction status: '{$paymentTransaction->getStatus()}'.\n";
            print "</pre>";
        }
    ));
    ```

    Service event handlers:
    * **PaymentProcessor::HANDLER_CATCH_EXCEPTION** - to handle exception if it is thrown
    * **PaymentProcessor::HANDLER_SAVE_CHANGES** - to save payment
    * **PaymentProcessor::HANDLER_FINISH_PROCESSING** - to display information about the payment after it was processed

4. <a name="stage_2_step_6"></a>Start processing of the data received with user when the user is redirected from payment form:

    ```php
    $paymentProcessor->processCustomerReturn(new CallbackResponse($_POST), $paymentTransaction);
    ```
    Following steps will be performed:
    1. Validation of the data received when the user is redirected from PaynetEasy payment form (superglobal array $_POST)
    2. Change payment transaction status **status** according to validated data
    3. Save payment transaction with event handler for  `PaymentProcessor::HANDLER_SAVE_PAYMENT`
    4. Display payment **status** and payment transaction **status** with event handler for `PaymentProcessor::HANDLER_FINISH_PROCESSING`
