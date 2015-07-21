# Простой пример использования библиотеки

Разберем выполнение запросов при [интеграции платежной формы](http://doc.payneteasy.com/doc/payment-form-integration.htm). Типовая обработка платежа происходит в три этапа. Первый и последний этапы происходят на стороне сервиса мерчанта, а второй - на стороне PaynetEasy.

1. Инициация оплаты:
    1. [Подключение загрузчика классов и необходимых классов](#stage_1_step_1)
    2. [Создание новой платежной транзакции](#stage_1_step_2)
    3. [Создание сервиса для обработки платежей](#stage_1_step_4)
    4. [Запуск обработки платежной транзакции](#stage_1_step_6)
        1. Проверка данных платежной транзакции и формирование на ее основе запроса к PaynetEasy
        3. Изменение статуса платежа **status**
        4. Выполнение запроса для старта обработки платежной транзакции и ее первичной проверки
        5. Получение ответа от PaynetEasy
        6. Изменение статуса платежной транзакции **status** на основе данных ответа
        7. Сохранение платежной транзакции
        8. Перенаправление клиента на платежную форму

2. Процессинг платежной формы:
    1. Заполнение клиентом платежной формы и отправка данных шлюзу PaynetEasy
    2. Обработка данных шлюзом
    3. Возврат пользователя на сервис мерчанта с передачей результата обработки платежной формы

3. Обработка результатов:
    1. [Подключение загрузчика классов и необходимых классов](#stage_2_step_1)
    2. [Загрузка сохраненной платежной транзакции](#stage_2_step_2)
    3. [Создание сервиса для обработки платежей](#stage_2_step_4)
    4. [Запуск обработки данных, полученных при возвращении пользователя с платежной формы](#stage_2_step_6)
        1. Проверка данных, полученные по возвращении клиента с платежной формы PaynetEasy
        2. Изменение статуса платежной транзакции **status**
        3. Сохранение платежной транзакции
        4. Вывод статуса платежа **status** и статуса платежной транзакции **status** на экран

Рассмотрим примеры исходного кода для выполнения обоих этапов. Код для выполнения второго этапа должен выполняться при переходе по ссылке, заданной в настройках по ключу **redirect_url**. Например, разместите исходный код первого этапа в файле `first_stage.php`, а второго - `second_stage.php`.

### <a name="stage_1"></a>Начало обработки платежной транзакции

1. <a name="stage_1_step_1"></a>Подключение загрузчика классов, [предоставляемого composer](http://getcomposer.org/doc/01-basic-usage.md#autoloading), и необходимых классов:

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
2. <a name="stage_1_step_2"></a>Создание новой платежной транзакции:
    ##### С использованием массивов, переданных в конструктор:

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
        // НЕВОЗМОЖНО задать и end_point и end_point_group для одного запроса
        'end_point'                 =>  253,
        'end_point_group'           =>  140,
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
    ##### С использованием сеттеров:

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
        // НЕВОЗМОЖНО задать и setEndPoint и setEndPointGroup для одного запроса
        ->setEndPoint(253)
        ->setEndPointGroup(140)
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

    Поля конфигурации запроса **QueryConfig**:
    * **[end_point](http://doc.payneteasy.com/doc/introduction.htm#Endpoint)** - точка входа для аккаунта мерчанта, выдается при подключении
    * **[end_point_group](http://doc.payneteasy.com/doc/introduction.htm#Endpoint)** - группа точек входа для аккаунта мерчанта, выдается при подключении
    * **[login](http://doc.payneteasy.com/doc/introduction.htm#PaynetEasy_Users)** - логин мерчанта для доступа к панели PaynetEasy, выдается при подключении
    * **signing_key** - ключ мерчанта для подписывания запросов, выдается при подключении
    * **[redirect_url](http://doc.payneteasy.com/doc/payment-form-integration.htm#Payment_Form_final_redirect)** - URL, на который пользователь будет перенаправлен после окончания запроса
    * **gateway_mode** - режим работы библиотеки: sandbox, production
    * **gateway_url_sandbox** - ссылка на шлюз PaynetEasy для режима работы sandbox
    * **gateway_url_production** - ссылка на шлюз PaynetEasy для режима работы production

3. <a name="stage_1_step_4"></a>Создание сервиса для обработки платежей:
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
            session_start();
            $_SESSION['payment_transaction'] = serialize($paymentTransaction);
        },
        PaymentProcessor::HANDLER_REDIRECT          => function(Response $response)
        {
            header("Location: {$response->getRedirectUrl()}");
            exit;
        }
    ));
    ```

    Обработчики событий для сервиса:
    * **PaymentProcessor::HANDLER_CATCH_EXCEPTION** - для обработки исключения, если оно было брошено
    * **PaymentProcessor::HANDLER_SAVE_CHANGES** - для сохранения платежной транзакции
    * **PaymentProcessor::HANDLER_REDIRECT** - для переадресации пользователя на URL платежной формы, полученный от PaynetEasy

4. <a name="stage_1_step_6"></a>Запуск обработки платежа:

    ```php
    $paymentProcessor->executeQuery('sale-form', $paymentTransaction);
    ```
    Будут выполнены следующие шаги:
    1. Проверка данных платежной транзакции и формирование на ее основе запроса к PaynetEasy
    3. Изменение статуса платежа **status**
    4. Выполнение запроса для старта обработки платежной транзакции и ее первичной проверки
    5. Получение ответа от PaynetEasy
    6. Изменение статуса платежной транзакции **status** на основе данных ответа
    7. Сохранение платежной транзакции обработчиком для `PaymentProcessor::HANDLER_SAVE_PAYMENT`
    8. Перенаправление клиента на платежную форму обработчиком для `PaymentProcessor::HANDLER_REDIRECT`

### <a name="stage_2"></a>Окончание обработки платежной транзакции

1. <a name="stage_2_step_1"></a>Подключение загрузчика классов, [предоставляемого composer](http://getcomposer.org/doc/01-basic-usage.md#autoloading), и необходимых классов:

    ```php
    require_once 'project/root/dir/vendor/autoload.php';

    use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
    use PaynetEasy\PaynetEasyApi\Transport\CallbackResponse;
    use PaynetEasy\PaynetEasyApi\PaymentProcessor;
    use Exception;
    ```
2. <a name="stage_2_step_2"></a>Загрузка сохраненной платежной транзакции:

    ```php
    session_start();
    $paymentTransaction = unserialize($_SESSION['payment_transaction']);
    ```

3. <a name="stage_2_step_4"></a>Создание сервиса для обработки платежей:

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

    Обработчики событий для сервиса:
    * **PaymentProcessor::HANDLER_CATCH_EXCEPTION** - для обработки исключения, если оно было брошено
    * **PaymentProcessor::HANDLER_SAVE_CHANGES** - для сохранения платежа
    * **PaymentProcessor::HANDLER_FINISH_PROCESSING** - для вывода информации о платеже после окончания обработки

4. <a name="stage_2_step_6"></a>Запуск обработки данных, полученных при возвращении пользователя с платежной формы:

    ```php
    $paymentProcessor->processCustomerReturn(new CallbackResponse($_POST), $paymentTransaction);
    ```
    Будут выполнены следующие шаги:
    1. Проверка данных, полученные по возвращении клиента с платежной формы PaynetEasy (суперглобальный массив $_POST)
    2. Изменение статуса платежной транзакции **status** на основе проверенных данных
    3. Сохранение платежной транзакции обработчиком для `PaymentProcessor::HANDLER_SAVE_PAYMENT`
    4. Вывод статуса платежа **status** и статуса платежной транзакции **status** на экран обработчиком для `PaymentProcessor::HANDLER_FINISH_PROCESSING`
