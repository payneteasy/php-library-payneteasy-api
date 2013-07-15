# PHP Library for Paynet API integration [![Build Status](https://travis-ci.org/payneteasy/php-library-paynet.png)](https://travis-ci.org/payneteasy/php-library-paynet)
## Доступная функциональность

Данная библиотека позволяет производить оплату с помощью [merchant PaynetEasy API](http://wiki.payneteasy.com/index.php/PnE:Merchant_API). На текущий момент реализованы следующие платежные методы:
* [Sale Transactions](http://wiki.payneteasy.com/index.php/PnE:Sale_Transactions)
* [Preauth/Capture Transactions](http://wiki.payneteasy.com/index.php/PnE:Preauth/Capture_Transactions)
* [Transfer Transactions](http://wiki.payneteasy.com/index.php/PnE:Transfer_Transactions)
* [Return Transactions](http://wiki.payneteasy.com/index.php/PnE:Return_Transactions)
* [Recurrent Transactions](http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions)
* [Payment Form Integration](http://wiki.payneteasy.com/index.php/PnE:Payment_Form_integration)

## Системные требования

* PHP 5.3 - 5.5
* [Расширение curl](http://php.net/manual/en/book.curl.php)

## Установка

1. [Установите composer](http://getcomposer.org/doc/00-intro.md), если его еще нет
2. Перейдите в папку проекта: `cd my/project/directory`
3. Создайте файл проекта для composer, если его еще нет: `composer init`
4. Добавьте библиотеку в зависимости проекта: `composer require payneteasy/php-library-payneteasy-api:~1.0`
5. Установите зависимости проекта: `composer install --prefer-dist`

## Запуск тестов

1. Перейдите в папку с библиотекой: `cd vendor/payneteasy/php-library-payneteasy-api/`
2. Запустите тесты: `phpunit -c tests/phpunit.xml tests`

## Использование

Разберем выполнение запросов при [интеграции платежной формы](http://wiki.payneteasy.com/index.php/PnE:Payment_Form_integration)

1. Подключите загрузчик классов, [предоставляемый composer](http://getcomposer.org/doc/01-basic-usage.md#autoloading), и необходимые классы:

    ```php
    require_once 'project/root/dir/vendor/autoload.php';

    use PaynetEasy\PaynetEasyApi\OrderData\Order;
    use PaynetEasy\PaynetEasyApi\OrderData\Customer;
    use PaynetEasy\PaynetEasyApi\Transport\Response;
    use PaynetEasy\PaynetEasyApi\OrderProcessor;
    ```
2. Создайте новый заказ покупателя или загрузите сохраненный заказ (например из сессии):

    ```php
    session_start();

    if (empty($_SESSION['order']))
    {
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
    }
    else
    {
        $order = unserialize($_SESSION['order']);
    }
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
    * **OrderProcessor::HANDLER_FINISH_PROCESSING** - для вывода информации о заказе после окончания обработки

    ```php
    $queryConfig = array
    (
        'end_point'             =>  253,
        'login'                 => 'rp-merchant1',
        'control'               => '3FD4E71A-D84E-411D-A613-40A0FB9DED3A',
        'redirect_url'          => "http://{$_SERVER['HTTP_HOST']}/{$_SERVER['REQUEST_URI']}"
    );

    $orderProcessor = new OrderProcessor('https://qa.clubber.me/paynet/api/v2/');

    $orderProcessor->setHandlers(array
    (
        OrderProcessor::HANDLER_SAVE_ORDER          => function(Order $order)
        {
            $_SESSION['order'] = serialize($order);
        },
        OrderProcessor::HANDLER_REDIRECT            => function(Order $order, Response $response)
        {
            header("Location: {$response->getRedirectUrl()}");
            exit;
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

4. Запустите обработку платежа. Будут выполнены следующие шаги:
    * Будет выполнен запрос к PaynetEasy для старта обработки платежа и его первичной проверки
    * Библиотека изменит статус платежа (**status**) и этап обработки платежа (**transportStage**) на основе данных ответа на запрос к PaynetEasy
    * Платеж будет сохранен в сессии обработчиком для `OrderProcessor::HANDLER_SAVE_ORDER`
    * Пользователь будет перенаправлен на платежную форму обработчиком для `OrderProcessor::HANDLER_REDIRECT`

    ```php
    $orderProcessor->executeWorkflow('sale-form', $queryConfig, $order, $_REQUEST);
    ```

5. После обработки платежной формы пользователь будет возвращен на ссылку, указанную в конфигурации с ключем **redirect_url**. Для обработки возврата будет использован тот же файл, что и для отправки пользователя на платежную форму. Будут выполнены следующие шаги:
    * Платеж будет загружен из сессии
    * Библиотека изменит статус платежа (**status**) и этап обработки платежа (**transportStage**) на основе данных, полученных по возвращении пользователя с платежной формы PaynetEasy (суперглобальный массив $_REQUEST)
    * Платеж будет сохранен в сессии обработчиком для `OrderProcessor::HANDLER_SAVE_ORDER`
    * Статус платежа (**status**) и его транспортный этап (**transportStage**) будут выведены на экран обработчиком для OrderProcessor::HANDLER_FINISH_PROCESSING