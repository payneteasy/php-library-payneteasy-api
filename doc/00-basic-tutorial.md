# Простой пример использования библиотеки

Разберем выполнение запросов при [интеграции платежной формы](http://wiki.payneteasy.com/index.php/PnE:Payment_Form_integration). Типовая обработка платежа, с точки зрения CMS мерчанта, происходит в два этапа.

1. Начало обработки платежа:
    * Конфигурирование библиотеки
    * Инициализация платежа
    * Отправка запроса к платежному шлюзу для начала обработки платежа
    * Изменение статуса платежа
    * Сохранение платежа в хранилище
    * Переадресация пользователя на платежную форму

2. Окончание обработки платежа:
    * Возврат пользователя с платежной формы
    * Конфигурирование библиотеки
    * Загрузка платежа из хранилища
    * Обработка данных, полученных при возвращении пользователя с платежной формы
    * Изменение статуса платежа
    * Сохранение платежа в хранилище
    * Вывод состояния платежа на экран

Рассмотрим примеры исходного кода для выполнения этих этапов. Обратите внимание, что для хранения платежа в примерах используется сессия.

### Начало обработки платежа

1. Подключите загрузчик классов, [предоставляемый composer](http://getcomposer.org/doc/01-basic-usage.md#autoloading), и необходимые классы:

    ```php
    require_once 'project/root/dir/vendor/autoload.php';

    use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
    use PaynetEasy\PaynetEasyApi\PaymentData\Customer;
    use PaynetEasy\PaynetEasyApi\Transport\Response;
    use PaynetEasy\PaynetEasyApi\PaymentProcessor;
    ```
2. Создайте новый платеж и покупателя:

    ```php
    $payment = new Payment(array
    (
        'client_payment_id'         => 'CLIENT-112244',
        'description'               => 'This is test payment',
        'amount'                    =>  9.99,
        'currency'                  => 'USD',
        'ipaddress'                 => '127.0.0.1'
    ));

    $payment->setCustomer(new Customer(array
    (
        'address'       => '2704 Colonial Drive',
        'city'          => 'Houston',
        'zip_code'      => '1235',
        'country'       => 'US',
        'email'         => 'vass.pupkin@example.com',
        'phone'         => '660-485-6353'
    )));
    ```
3. Создайте конфигурацию для выполнения запроса и сервис для обработки платежей. Назначьте обработчики событий для сервиса:

    Поля кофигурации:
    * **[end_point](http://wiki.payneteasy.com/index.php/PnE:Introduction#Endpoint)** - точка входа для аккаунта мерчанта, выдается при подключении
    * **[login](http://wiki.payneteasy.com/index.php/PnE:Introduction#PaynetEasy_Users)** - логин мерчанта для доступа к панели PaynetEasy, выдается при подключении
    * **control** - ключ мерчанта для подписывания запросов, выдается при подключении
    * **[redirect_url](http://wiki.payneteasy.com/index.php/PnE:Payment_Form_integration#Payment_Form_final_redirect)** - URL на который пользователь будет перенаправлен после окончания запроса

    Обработчики:
    * **PaymentProcessor::HANDLER_SAVE_PAYMENT** - для сохранения платежа
    * **PaymentProcessor::HANDLER_REDIRECT** - для переадресации пользователя на URL платежной формы, полученный от PaynetEasy

    ```php
    $queryConfig = array
    (
        'end_point'             =>  253,
        'login'                 => 'rp-merchant1',
        'control'               => '3FD4E71A-D84E-411D-A613-40A0FB9DED3A',
        'redirect_url'          => "http://{$_SERVER['HTTP_HOST']}/{$_SERVER['REQUEST_URI']}"
    );

    $paymentProcessor = new PaymentProcessor('https://payment.domain.com/paynet/api/v2/');

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

4. Запустите обработку платежа. Будут выполнены следующие шаги:
    * Библиотека проверит данные платежа и сформирует на его основе запрос к PaynetEasy
    * Запрос будет выполнен для старта обработки платежа и его первичной проверки, будет получен ответ от PaynetEasy
    * Библиотека изменит статус платежа **status** и этап обработки платежа **processingStage** на основе данных ответа
    * Платеж будет сохранен в сессии обработчиком для `PaymentProcessor::HANDLER_SAVE_PAYMENT`
    * Пользователь будет перенаправлен на платежную форму обработчиком для `PaymentProcessor::HANDLER_REDIRECT`

    ```php
    $paymentProcessor->executeWorkflow('sale-form', $queryConfig, $payment);
    ```
### Окончание обработки платежа

1. Подключите загрузчик классов, [предоставляемый composer](http://getcomposer.org/doc/01-basic-usage.md#autoloading), и необходимые классы:

    ```php
    require_once 'project/root/dir/vendor/autoload.php';

    use PaynetEasy\PaynetEasyApi\Transport\Response;
    use PaynetEasy\PaynetEasyApi\PaymentProcessor;
    ```
2. Загрузите сохраненный платеж:

    ```php
    session_start();
    $payment = unserialize($_SESSION['payment']);
    ```
3. Создайте конфигурацию для выполнения запроса и сервис для обработки платежей. Назначьте обработчики событий для сервиса:

    Поля кофигурации:
    * **control** - ключ мерчанта для подписывания запросов, выдается при подключении

    Обработчики:
    * **PaymentProcessor::HANDLER_SAVE_PAYMENT** - для сохранения платежа
    * **PaymentProcessor::HANDLER_FINISH_PROCESSING** - для вывода информации о платеже после окончания обработки

    ```php
    $queryConfig = array
    (
        'control'               => '3FD4E71A-D84E-411D-A613-40A0FB9DED3A',
    );

    $paymentProcessor = new PaymentProcessor('https://payment.domain.com/paynet/api/v2/');

    $paymentProcessor->setHandlers(array
    (
        PaymentProcessor::HANDLER_SAVE_PAYMENT          => function(Payment $payment)
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
5. Запустите обработку данных, полученных при возвращении пользователя с платежной формы. Будут выполнены следующие шаги:
    * Библиотека проверит данные, полученные по возвращении пользователя с платежной формы PaynetEasy (суперглобальный массив $_REQUEST)
    * Библиотека изменит статус платежа **status** и этап обработки платежа **processingStage** на основе проверенных данных
    * Платеж будет сохранен в сессии обработчиком для `PaymentProcessor::HANDLER_SAVE_PAYMENT`
    * Статус платежа **status** и этап обработки платежа **processingStage** будут выведены на экран обработчиком для PaymentProcessor::HANDLER_FINISH_PROCESSING

    ```php
    $paymentProcessor->executeWorkflow('sale-form', $queryConfig, $payment, $_REQUEST);
    ```