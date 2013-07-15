# Простой пример использования библиотеки

Разберем выполнение запросов при [интеграции платежной формы](http://wiki.payneteasy.com/index.php/PnE:Payment_Form_integration). Типовая обработка заказа, с точки зрения CMS мерчанта, происходит в два этапа.

1. Начало обработки заказа:
    * Конфигурирование библиотеки
    * Инициализация заказа
    * Отправка запроса к платежному шлюзу для начала обработки заказа
    * Изменение статуса заказа
    * Сохранение заказа в хранилище
    * Переадресация пользователя на платежную форму

2. Окончание обработки заказа:
    * Возврат пользователя с платежной формы
    * Конфигурирование библиотеки
    * Загрузка заказа из хранилища
    * Обработка данных, полученных при возвращении пользователя с платежной формы
    * Изменение статуса заказа
    * Сохранение заказа в хранилище
    * Вывод состояния заказа на экран

Рассмотрим примеры исходного кода для выполнения этих этапов. Обратите внимание, что для хранения заказа в примерах используется сессия.

### Начало обработки заказа

1. Подключите загрузчик классов, [предоставляемый composer](http://getcomposer.org/doc/01-basic-usage.md#autoloading), и необходимые классы:

    ```php
    require_once 'project/root/dir/vendor/autoload.php';

    use PaynetEasy\PaynetEasyApi\OrderData\Order;
    use PaynetEasy\PaynetEasyApi\OrderData\Customer;
    use PaynetEasy\PaynetEasyApi\Transport\Response;
    use PaynetEasy\PaynetEasyApi\OrderProcessor;
    ```
2. Создайте новый заказ и покупателя:

    ```php
    $order = new Order(array
    (
        'client_orderid'            => 'CLIENT-112244',
        'desc'                      => 'This is test order',
        'amount'                    =>  9.99,
        'currency'                  => 'USD',
        'ipaddress'                 => '127.0.0.1'
    ));

    $order->setCustomer(new Customer(array
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
    * **OrderProcessor::HANDLER_SAVE_ORDER** - для сохранения заказа
    * **OrderProcessor::HANDLER_REDIRECT** - для переадресации пользователя на URL платежной формы, полученный от PaynetEasy

    ```php
    $queryConfig = array
    (
        'end_point'             =>  253,
        'login'                 => 'rp-merchant1',
        'control'               => '3FD4E71A-D84E-411D-A613-40A0FB9DED3A',
        'redirect_url'          => "http://{$_SERVER['HTTP_HOST']}/{$_SERVER['REQUEST_URI']}"
    );

    $orderProcessor = new OrderProcessor('https://payment.domain.com/paynet/api/v2/');

    $orderProcessor->setHandlers(array
    (
        OrderProcessor::HANDLER_SAVE_ORDER          => function(Order $order)
        {
            start_session();
            $_SESSION['order'] = serialize($order);
        },
        OrderProcessor::HANDLER_REDIRECT            => function(Order $order, Response $response)
        {
            header("Location: {$response->getRedirectUrl()}");
            exit;
        }
    ));
    ```

4. Запустите обработку платежа. Будут выполнены следующие шаги:
    * Библиотека проверит данные платежа и сформирует на его основе запрос к PaynetEasy
    * Запрос будет выполнен для старта обработки платежа и его первичной проверки, будет получен ответ от PaynetEasy
    * Библиотека изменит статус платежа **status** и этап обработки платежа **transportStage** на основе данных ответа
    * Платеж будет сохранен в сессии обработчиком для `OrderProcessor::HANDLER_SAVE_ORDER`
    * Пользователь будет перенаправлен на платежную форму обработчиком для `OrderProcessor::HANDLER_REDIRECT`

    ```php
    $orderProcessor->executeWorkflow('sale-form', $queryConfig, $order);
    ```
### Окончание обработки заказа

1. Подключите загрузчик классов, [предоставляемый composer](http://getcomposer.org/doc/01-basic-usage.md#autoloading), и необходимые классы:

    ```php
    require_once 'project/root/dir/vendor/autoload.php';

    use PaynetEasy\PaynetEasyApi\Transport\Response;
    use PaynetEasy\PaynetEasyApi\OrderProcessor;
    ```
2. Загрузите сохраненный заказ:

    ```php
    session_start();
    $order = unserialize($_SESSION['order']);
    ```
3. Создайте конфигурацию для выполнения запроса и сервис для обработки платежей. Назначьте обработчики событий для сервиса:

    Поля кофигурации:
    * **control** - ключ мерчанта для подписывания запросов, выдается при подключении

    Обработчики:
    * **OrderProcessor::HANDLER_SAVE_ORDER** - для сохранения заказа
    * **OrderProcessor::HANDLER_FINISH_PROCESSING** - для вывода информации о заказе после окончания обработки

    ```php
    $queryConfig = array
    (
        'control'               => '3FD4E71A-D84E-411D-A613-40A0FB9DED3A',
    );

    $orderProcessor = new OrderProcessor('https://payment.domain.com/paynet/api/v2/');

    $orderProcessor->setHandlers(array
    (
        OrderProcessor::HANDLER_SAVE_ORDER          => function(Order $order)
        {
            $_SESSION['order'] = serialize($order);
        },
        OrderProcessor::HANDLER_FINISH_PROCESSING   => function(Order $order)
        {
            print "<pre>";
            print_r("Order state: {$order->getTransportStage()}\n");
            print_r("Order status: {$order->getStatus()}\n");
            print "</pre>";
        }
    ));
    ```
5. Запустите обработку данных, полученных при возвращении пользователя с платежной формы. Будут выполнены следующие шаги:
    * Библиотека проверит данные, полученные по возвращении пользователя с платежной формы PaynetEasy (суперглобальный массив $_REQUEST)
    * Библиотека изменит статус платежа **status** и этап обработки платежа **transportStage** на основе проверенных данных
    * Платеж будет сохранен в сессии обработчиком для `OrderProcessor::HANDLER_SAVE_ORDER`
    * Статус платежа **status** и этап обработки платежа **transportStage** будут выведены на экран обработчиком для OrderProcessor::HANDLER_FINISH_PROCESSING

    ```php
    $orderProcessor->executeWorkflow('sale-form', $queryConfig, $order, $_REQUEST);
    ```