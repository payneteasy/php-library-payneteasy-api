# Простой пример использования библиотеки

Разберем выполнение запросов при [интеграции платежной формы](http://wiki.payneteasy.com/index.php/PnE:Payment_Form_integration). Типовая обработка платежа происходит в три этапа. Первый и последний этапы происходят на стороне сервиса мерчанта, а второй - на стороне PaynetEasy.

1. Инициация оплаты:
    1. [Подключение загрузчика классов и необходимых классов](#stage_1_step_1)
    2. [Создание нового платежа, покупателя и адреса](#stage_1_step_2)
    3. [Создание конфигурации для выполнения запроса](#stage_1_step_3)
    4. [Создание сервиса для обработки платежей](#stage_1_step_4)
    5. [Установка обработчиков событий для сервиса](#stage_1_step_5)
    6. [Запуск обработки платежа](#stage_1_step_6)
        1. Проверка данных платежа и формирование на его основе запроса к PaynetEasy
        2. Выполнение запроса для старта обработки платежа и его первичной проверки
        3. Получение ответа от PaynetEasy
        4. Изменение статуса платежа **status** и этапа обработки платежа **processingStage** на основе данных ответа
        5. Сохранение платежа
        6. Перенаправление клиента на платежную форму

2. Процессинг платежной формы:
    1. Заполнение клиентом платежной формы и отправка данных шлюзу PaynetEasy
    2. Обработка данных шлюзом
    3. Возврат пользователя на сервис мерчанта с передачей результата обработки платежной формы

3. Обработка результатов:
    1. [Подключение загрузчика классов и необходимых классов](#stage_2_step_1)
    2. [Загрузка сохраненного платежа](#stage_2_step_2)
    3. [Создание конфигурации для обработки результата процессинга платежной формы](#stage_2_step_3)
    4. [Создание сервиса для обработки платежей](#stage_2_step_4)
    5. [Установка обработчиков событий для сервиса](#stage_2_step_5)
    6. [Запуск обработки данных, полученных при возвращении пользователя с платежной формы](#stage_2_step_6)
        1. Проверка данных, полученные по возвращении клиента с платежной формы PaynetEasy
        2. Изменение статуса платежа **status** и этапа обработки платежа **processingStage** на основе проверенных данных
        3. Сохранение платежа обработчиком
        4. Вывод статуса платежа **status** и этапа обработки платежа **processingStage** на экран

## Раздельная обработка этапов

Рассмотрим примеры исходного кода для раздельного выполнения обоих этапов. Код для выполнения второго этапа должен находиться в отдельном файле, доступном по ссылке, заданной в настройкам по ключу **redirect_url**.

### <a name="stage_1"></a>Начало обработки платежа

1. <a name="stage_1_step_1"></a>Подключение загрузчика классов, [предоставляемого composer](http://getcomposer.org/doc/01-basic-usage.md#autoloading), и необходимых классов:

    ```php
    require_once 'project/root/dir/vendor/autoload.php';

    use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
    use PaynetEasy\PaynetEasyApi\PaymentData\Customer;
    use PaynetEasy\PaynetEasyApi\Transport\Response;
    use PaynetEasy\PaynetEasyApi\PaymentProcessor;
    ```
2. <a name="stage_1_step_2"></a>Создание нового платежа, покупателя и адреса:
    ##### С использованием массивов, переданных в конструктор:

    ```php
    $customer = new Customer(array
    (
        'email'             => 'vass.pupkin@example.com',
        'ip_address'        => '127.0.0.1'
    ));

    $billingAddress = new BillingAddress(array
    (
        'country'           => 'US',
        'city'              => 'Houston',
        'first_line'        => '2704 Colonial Drive',
        'zip_code'          => '1235',
        'phone'             => '660-485-6353'
    ));

    $payment = new Payment(array
    (
        'client_payment_id' => 'CLIENT-112244',
        'description'       => 'This is test payment',
        'amount'            =>  9.99,
        'currency'          => 'USD',
        'customer'          => $customer,
        'billing_address'   => $billingAddress
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
        ->setCity('Houston')
        ->setFirstLine('2704 Colonial Drive')
        ->setZipCode('1235')
        ->setPhone('660-485-6353')
    ;

    $payment = (new Payment)
        ->setClientPaymentId('CLIENT-112244')
        ->setDescription('This is test payment')
        ->setAmount(9.99)
        ->setCurrency('USD')
        ->setCustomer($customer)
        ->setBillingAddress($billingAddress)
    ;
    ```

3. <a name="stage_1_step_3"></a>Создание конфигурации для выполнения запроса:

    ```php
    $queryConfig = array
    (
        'end_point'             =>  253,
        'login'                 => 'rp-merchant1',
        'control'               => '3FD4E71A-D84E-411D-A613-40A0FB9DED3A',
        'redirect_url'          => "http://{$_SERVER['HTTP_HOST']}/second_stage.php"
    );
    ```
    Поля конфигурации:
    * **[end_point](http://wiki.payneteasy.com/index.php/PnE:Introduction#Endpoint)** - точка входа для аккаунта мерчанта, выдается при подключении
    * **[login](http://wiki.payneteasy.com/index.php/PnE:Introduction#PaynetEasy_Users)** - логин мерчанта для доступа к панели PaynetEasy, выдается при подключении
    * **control** - ключ мерчанта для подписывания запросов, выдается при подключении
    * **[redirect_url](http://wiki.payneteasy.com/index.php/PnE:Payment_Form_integration#Payment_Form_final_redirect)** - URL, на который пользователь будет перенаправлен после окончания запроса

4. <a name="stage_1_step_4"></a>Создание сервиса для обработки платежей:
    ```php
    $paymentProcessor = new PaymentProcessor('https://payment.domain.com/paynet/api/v2/');
    ```
5. <a name="stage_1_step_5"></a>Установка обработчиков событий для сервиса:

    ```php
    $paymentProcessor->setHandlers(array
    (
        PaymentProcessor::HANDLER_SAVE_PAYMENT          => function(Payment $payment)
        {
            start_session();
            $_SESSION['payment'] = serialize($payment);
        },
        PaymentProcessor::HANDLER_REDIRECT            => function(Payment $payment, Response $response)
        {
            header("Location: {$response->getRedirectUrl()}");
            exit;
        }
    ));
    ```
    Обработчики:
    * **PaymentProcessor::HANDLER_SAVE_PAYMENT** - для сохранения платежа
    * **PaymentProcessor::HANDLER_REDIRECT** - для переадресации пользователя на URL платежной формы, полученный от PaynetEasy

6. <a name="stage_1_step_6"></a>Запуск обработки платежа:

    ```php
    $paymentProcessor->executeQuery('sale-form', $queryConfig, $payment);
    ```
    Будут выполнены следующие шаги:
    1. Проверка данных платежа и формирование на его основе запроса к PaynetEasy
    2. Выполнение запроса для старта обработки платежа и его первичной проверки
    3. Получение ответа от PaynetEasy
    4. Изменение статуса платежа **status** и этапа обработки платежа **processingStage** на основе данных ответа
    5. Сохранение платежа обработчиком для `PaymentProcessor::HANDLER_SAVE_PAYMENT`
    6. Перенаправление клиента на платежную форму обработчиком для `PaymentProcessor::HANDLER_REDIRECT`

### <a name="stage_2"></a>Окончание обработки платежа

1. <a name="stage_2_step_1"></a>Подключение загрузчика классов, [предоставляемого composer](http://getcomposer.org/doc/01-basic-usage.md#autoloading), и необходимых классов:

    ```php
    require_once 'project/root/dir/vendor/autoload.php';

    use PaynetEasy\PaynetEasyApi\Transport\Response;
    use PaynetEasy\PaynetEasyApi\PaymentProcessor;
    ```
2. <a name="stage_2_step_2"></a>Загрузка сохраненного платежа:

    ```php
    session_start();
    $payment = unserialize($_SESSION['payment']);
    ```
3. <a name="stage_2_step_3"></a>Создание конфигурации для обработки результата процессинга платежной формы:

    ```php
    $callbackConfig = array
    (
        'control'               => '3FD4E71A-D84E-411D-A613-40A0FB9DED3A',
    );
    ```
    Поля конфигурации:
    * **control** - ключ мерчанта для подписывания запросов, выдается при подключении

4. <a name="stage_2_step_4"></a>Создание сервиса для обработки платежей:

    ```php
    $paymentProcessor = new PaymentProcessor('https://payment.domain.com/paynet/api/v2/');
    ```

5. <a name="stage_2_step_5"></a>Установка обработчиков событий для сервиса:

    ```php
    $paymentProcessor->setHandlers(array
    (
        PaymentProcessor::HANDLER_SAVE_PAYMENT        => function(Payment $payment)
        {
            $_SESSION['payment'] = serialize($payment);
        },
        PaymentProcessor::HANDLER_FINISH_PROCESSING   => function(Payment $payment)
        {
            print "<pre>";
            print_r("Payment state: {$payment->getProcessingStage()}\n");
            print_r("Payment status: {$payment->getStatus()}\n");
            print "</pre>";
        }
    ));
    ```
    Обработчики:
    * **PaymentProcessor::HANDLER_SAVE_PAYMENT** - для сохранения платежа
    * **PaymentProcessor::HANDLER_FINISH_PROCESSING** - для вывода информации о платеже после окончания обработки

6. <a name="stage_2_step_6"></a>Запуск обработки данных, полученных при возвращении пользователя с платежной формы:

    ```php
    $paymentProcessor->executeCallback($_REQUEST, $callbackConfig, $payment);
    ```
    Будут выполнены следующие шаги:
    1. Проверка данных, полученные по возвращении клиента с платежной формы PaynetEasy (суперглобальный массив $_REQUEST)
    2. Изменение статуса платежа **status** и этапа обработки платежа **processingStage** на основе проверенных данных
    3. Сохранение платежа обработчиком для `PaymentProcessor::HANDLER_SAVE_PAYMENT`
    4. Вывод статуса платежа **status** и этапа обработки платежа **processingStage** на экран обработчиком для `PaymentProcessor::HANDLER_FINISH_PROCESSING`

## Универсальный код для обработки обоих этапов

Как можно заметить, оба этапа обработки платежа выполняются похожим исходным кодом. Поэтому возможно создать универсальный код для обоих этапов. Обратите внимание, что код для выполнения второго этапа находится в том же файле, что и для первого, и этот файл должен быть доступен по ссылке, заданной в настройкам по ключу **redirect_url**.

1. Подключение загрузчика классов, [предоставляемого composer](http://getcomposer.org/doc/01-basic-usage.md#autoloading), и необходимых классов:

    ```php
    require_once 'project/root/dir/vendor/autoload.php';

    use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
    use PaynetEasy\PaynetEasyApi\PaymentData\Customer;
    use PaynetEasy\PaynetEasyApi\Transport\Response;
    use PaynetEasy\PaynetEasyApi\PaymentProcessor;
    ```
2. Создание нового платежа, покупателя и адреса или загрузка сохраненного платежа:
    ##### С использованием массивов, переданных в конструктор:

    ```php
    session_start();

    if (isset($_SESSION['payment']
    {
        $payment = unserialize($_SESSION['payment']);
    }
    else
    {
        $customer = new Customer(array
        (
            'email'             => 'vass.pupkin@example.com',
            'ip_address'        => '127.0.0.1'
        ));

        $billingAddress = new BillingAddress(array
        (
            'country'           => 'US',
            'city'              => 'Houston',
            'first_line'        => '2704 Colonial Drive',
            'zip_code'          => '1235',
            'phone'             => '660-485-6353'
        ));

        $payment = new Payment(array
        (
            'client_payment_id' => 'CLIENT-112244',
            'description'       => 'This is test payment',
            'amount'            =>  9.99,
            'currency'          => 'USD',
            'customer'          => $customer,
            'billing_address'   => $billingAddress
        ));
    }
    ```
    ##### С использованием сеттеров:

    ```php
    session_start();

    if (isset($_SESSION['payment']
    {
        $payment = unserialize($_SESSION['payment']);
    }
    else
    {
        $customer = (new Customer)
            ->setEmail('vass.pupkin@example.com')
            ->setIpAddress('127.0.0.1')
        ;

        $billingAddress = (new BillingAddress)
            ->setCountry('US')
            ->setCity('Houston')
            ->setFirstLine('2704 Colonial Drive')
            ->setZipCode('1235')
            ->setPhone('660-485-6353')
        ;

        $payment = (new Payment)
            ->setClientPaymentId('CLIENT-112244')
            ->setDescription('This is test payment')
            ->setAmount(9.99)
            ->setCurrency('USD')
            ->setCustomer($customer)
            ->setBillingAddress($billingAddress)
        ;
    }
    ```
3. Создание конфигурации для выполнения запроса:

    ```php
    $queryConfig = array
    (
        'end_point'             =>  253,
        'login'                 => 'rp-merchant1',
        'control'               => '3FD4E71A-D84E-411D-A613-40A0FB9DED3A',
        'redirect_url'          => "http://{$_SERVER['HTTP_HOST']}/{$_SERVER['REQUEST_URI']}"
    );
    ```
    Поля конфигурации:
    * **[end_point](http://wiki.payneteasy.com/index.php/PnE:Introduction#Endpoint)** - точка входа для аккаунта мерчанта, выдается при подключении
    * **[login](http://wiki.payneteasy.com/index.php/PnE:Introduction#PaynetEasy_Users)** - логин мерчанта для доступа к панели PaynetEasy, выдается при подключении
    * **control** - ключ мерчанта для подписывания запросов, выдается при подключении
    * **[redirect_url](http://wiki.payneteasy.com/index.php/PnE:Payment_Form_integration#Payment_Form_final_redirect)** - URL, на который пользователь будет перенаправлен после окончания запроса

4. Создание сервиса для обработки платежей:
    ```php
    $paymentProcessor = new PaymentProcessor('https://payment.domain.com/paynet/api/v2/');
    ```
5. Установка обработчиков событий для сервиса:

    ```php
    $paymentProcessor->setHandlers(array
    (
        PaymentProcessor::HANDLER_SAVE_PAYMENT        => function(Payment $payment)
        {
            start_session();
            $_SESSION['payment'] = serialize($payment);
        },
        PaymentProcessor::HANDLER_REDIRECT            => function(Payment $payment, Response $response)
        {
            header("Location: {$response->getRedirectUrl()}");
            exit;
        },
        PaymentProcessor::HANDLER_FINISH_PROCESSING   => function(Payment $payment)
        {
            print "<pre>";
            print_r("Payment state: {$payment->getProcessingStage()}\n");
            print_r("Payment status: {$payment->getStatus()}\n");
            print "</pre>";
        }
    ));
    ```
    Обработчики:
    * **PaymentProcessor::HANDLER_SAVE_PAYMENT** - для сохранения платежа
    * **PaymentProcessor::HANDLER_REDIRECT** - для переадресации пользователя на URL платежной формы, полученный от PaynetEasy
    * **PaymentProcessor::HANDLER_FINISH_PROCESSING** - для вывода информации о платеже после окончания обработки

6. Запуск обработки платежа:

    ```php
    $paymentProcessor->executeWorkflow('sale-form', $queryConfig, $payment);
    ```
    ##### Шаги, выполняемые для первого этапа:
    1. Проверка данных платежа и формирование на его основе запроса к PaynetEasy
    2. Выполнение запроса для старта обработки платежа и его первичной проверки
    3. Получение ответа от PaynetEasy
    4. Изменение статуса платежа **status** и этапа обработки платежа **processingStage** на основе данных ответа
    5. Сохранение платежа обработчиком для `PaymentProcessor::HANDLER_SAVE_PAYMENT`
    6. Перенаправление клиента на платежную форму обработчиком для `PaymentProcessor::HANDLER_REDIRECT`

    ##### Шаги, выполняемые для второго этапа:
    1. Проверка данных, полученные по возвращении клиента с платежной формы PaynetEasy (суперглобальный массив $_REQUEST)
    2. Изменение статуса платежа **status** и этапа обработки платежа **processingStage** на основе проверенных данных
    3. Сохранение платежа обработчиком для `PaymentProcessor::HANDLER_SAVE_PAYMENT`
    4. Вывод статуса платежа **status** и этапа обработки платежа **processingStage** на экран обработчиком для `PaymentProcessor::HANDLER_FINISH_PROCESSING`
