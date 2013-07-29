# Фронтенд библиотеки, PaymentProcessor

Фронтенд библиотеки представляет класс **[PaynetEasy\PaynetEasyApi\PaymentProcessor](../../source/PaynetEasy/PaynetEasyApi/PaymentProcessor.php)**. Класс предоставляет следующие возможности:
* **[executeQuery()](#executeQuery)**: простое выполнение запроса к PaynetEasy
* **[processCustomerReturn()](#processCustomerReturn)**: простая обработка данных, полученных от PaynetEasy при возвращении пользователя с платежного шлюза
* **[processPaynetEasyCallback()](#processPaynetEasyCallback)**: простая обработка данных, полученных от PaynetEasy при поступлении коллбэка от PaynetEasy
* **[setHandlers()](#setHandlers)**: установка обработчиков для различных событий, происходящих при обработке заказа

### <a name="executeQuery"></a>executeQuery(): простое выполнение запроса к PaynetEasy

Некоторые сценарии обработки платежа имеют асинхронную природу и состоят из нескольких запросов. Так, некоторые запросы не возвращают результат платежа сразу и требуют многократного выполнения запроса **status**, после которого клиент может быть отправлен на шлюз PaynetEasy для проведения дополнительных шагов авторизации. После возвращения клиента на сервис мерчанта необходима обработка данных, полученных от шлюза.
<a name="async_queries_list"></a>Cписок асинхронных запросов:
* sale
* preauth
* capture
* return
* make-rebill
* transfer-by-ref

Ознакомиться с обработкой таких запросов можно в следующих файлах:
* [Пример выполнения запроса sale](../../example/sale.php)
* [Пример выполнения запроса preauth](../../example/preauth.php)
* [Пример выполнения запроса capture](../../example/capture.php)
* [Пример выполнения запроса return](../../example/return.php)
* [Пример выполнения запроса make-rebill](../../example/make-rebill.php)
* [Пример выполнения запроса transfer-by-ref](../../example/transfer-by-ref.php)

Отдельный сценарий обработки необходим и при интеграции платежной формы. Запрос к шлюзу возвращает ссылку на платежную форму, на которую должен быть отправлен клиент. После заполнения и отправки данных шлюз обрабатывает платежную форму и возвращает клиента на сервис мерчанта. После возвращения клиента на сервис мерчанта необходима обработка данных, полученных от шлюза.
<a name="form_queries_list"></a>Список запросов для интеграции платежной формы:
* sale-form
* preauth-form
* transfer-form

Ознакомиться с обработкой таких запросов можно в следующих файлах:
* [Пример выполнения запроса sale-form](../../example/sale-form.php)
* [Пример выполнения запроса preauth-form](../../example/preauth-form.php)
* [Пример выполнения запроса transfer-form](../../example/transfer-form.php)

Некоторые операции с платежами не требуют сложных сценариев обработки и выполняются с помощью одного запроса.
Список простых операций над платежом:
* create-card-ref
* get-card-info
* status

Ознакомиться с обработкой таких запросов можно в следующих файлах:
* [Пример выполнения запроса create-card-ref](../../example/create-card-ref.php)
* [Пример выполнения запроса get-card-info](../../example/get-card-info.php)
* [Пример выполнения запроса status](../../example/status.php)

Для удобного выполнения запросов к PaynetEasy в **PaymentProcessor** реализован метод **[executeQuery()](../../source/PaynetEasy/PaynetEasyApi/PaymentProcessor.php#L114)**.
Метод принимает два параметра:
* Название запроса
* Платеж для обработки

### <a name="processCustomerReturn"></a>processCustomerReturn(): простая обработка данных, полученных от PaynetEasy

Каждый [асинхронный запрос](#async_queries_list) может завершиться перенаправлением пользователя на платежный шлюз для выполнения дополнительных действий, а каждый [запрос для интеграции платежной формы](#form_queries_list) обязательно содержит такое перенаправление. Каждый раз при возвращении пользователя на сервис мерчанта передаются данные с результатом обработки платежа. Также, если в [конфигурации стартового запроса](../00-basic-tutorial.md#stage_1_step_3) был задан ключ **server_callback_url**, то через некоторое время PaynetEasy вызовет этот url и передаст ему данные, описанные в wiki PaynetEasy в разделе [Merchant Callbacks](http://wiki.payneteasy.com/index.php/PnE:Merchant_Callbacks). Для удобной обработки этих данных в **PaymentProcessor** реализован метод **[executeCallback()](../../source/PaynetEasy/PaynetEasyApi/PaymentProcessor.php#L161)**.
Метод принимает два параметра:
* Объект с данными, полученными при возвращении пользователя от PaynetEasy
* Платеж для обработки

Ознакомиться с использованием данного метода можно в следующих файлах:
* [Базовый пример использования библиотеки](../00-basic-tutorial.md#stage_2)
* [Пример выполнения запроса sale](../../example/sale.php#L91)
* [Пример выполнения запроса preauth](../../example/preauth.php#L91)
* [Пример выполнения запроса sale-form](../../example/sale-form.php#L70)
* [Пример выполнения запроса preauth-form](../../example/preauth-form.php#70)
* [Пример выполнения запроса transfer-form](../../example/transfer-form.php#70)

### <a name="processPaynetEasyCallback"></a>processPaynetEasyCallback(): простая обработка данных, полученных от PaynetEasy

После выполнения [асинхронного запроса](#async_queries_list) или [запроса для интеграции платежной формы](#form_queries_list), если в [конфигурации стартового запроса](../00-basic-tutorial.md#stage_1_step_3) был задан ключ **server_callback_url**, то через некоторое время PaynetEasy вызовет этот url и передаст ему данные, описанные в wiki PaynetEasy в разделе [Merchant Callbacks](http://wiki.payneteasy.com/index.php/PnE:Merchant_Callbacks). Для удобной обработки этих данных в **PaymentProcessor** реализован метод **[processPaynetEasyCallback()](../../source/PaynetEasy/PaynetEasyApi/PaymentProcessor.php#L176)**.
Метод принимает два параметра:
* Объект с данными, полученными при возвращении пользователя от PaynetEasy
* Платеж для обработки

Ознакомиться с использованием данного метода можно в следующих файлах:
* [Пример выполнения запроса sale](../../example/sale.php#L102)
* [Пример выполнения запроса preauth](../../example/preauth.php#L102)
* [Пример выполнения запроса sale-form](../../example/sale-form.php#L81)
* [Пример выполнения запроса preauth-form](../../example/preauth-form.php#81)
* [Пример выполнения запроса transfer-form](../../example/transfer-form.php#81)

### <a name="setHandlers"></a> setHandlers(): установка обработчиков для различных событий, происходящих при обработке заказа

**PaymentProcessor** скрывает от конечного пользователя алгоритм обработки заказа в методах **[executeWorkflow()](#executeWorkflow)**, **[executeQuery()](#executeQuery)** и **[executeCallback()](#executeCallback)**. При этом во время обработки заказа возникают ситуации, обработка которых должна быть реализована на стороне сервиса мерчанта. Для обработки таких ситуаций в **PaymentProcessor** реализована система событий и их обработчиков. Обработчики могут быть установлены тремя разными способами:
* Передача массива обработчиков в [конструктор класса **PaymentProcessor**](../../source/PaynetEasy/PaynetEasyApi/PaymentProcessor.php#L101)
* Передача массива обработчиков в метод [**setHandlers()**](../../source/PaynetEasy/PaynetEasyApi/PaymentProcessor.php#L273)
* Установка обработчиков по одному с помощью метода **[setHandler()](../../source/PaynetEasy/PaynetEasyApi/PaymentProcessor.php#L249)**

Список обработчиков событий:
* **HANDLER_SAVE_PAYMENT** - обработчик для сохранения платежа. Вызывается, если данные платежа изменены. Должен реализовывать сохранение платежа в хранилище. Принимает следующие параметры:
    * Платеж
    * Ответ от PaynetEasy (опционально, не доступен, если произошла ошибка на этапе формирования или выполнения запроса к PaynetEasy)
* **HANDLER_STATUS_UPDATE** - обработчик для обновления статуса платежа. Вызывается, если статус платежа не изменился с момента последней проверки. Должен реализовывать запуск проверки статуса платежа. Принимает следующие параметры:
    * Ответ от PaynetEasy
    * Платеж
* **HANDLER_SHOW_HTML** - обработчик для вывода HTML-кода, полученного от PaynetEasy. Вызывается, если необходима 3D-авторизация пользователя. Должен реализовывать вывод HTML-кода из ответа от PaynetEasy в браузер клиента. Принимает следующие параметры:
    * Ответ от PaynetEasy
    * Платеж
* **HANDLER_REDIRECT** - обработчик для перенаправления клиента на платежную форму PaynetEasy. Вызывается после выполнения запроса [sale-form, preauth-form или transfer-form](../payment-scenarios/05-payment-form-integration.md). Должен реализовывать перенаправление пользователя на URL из ответа от PaynetEasy. Принимает следующие параметры:
    * Ответ от PaynetEasy
    * Платеж
* **HANDLER_FINISH_PROCESSING** - обработчик для дальнейшей обработки платежа сервисом мерчанта после завершения обработки на стороне PaynetEasy. Вызывается после того, как платеж полностью обработан на стороне PaynetEasy. Принимает следующие параметры:
    * Платеж
    * Ответ от PaynetEasy (опционально, не доступен, если обработка платежа уже была завершена ранее)
* **HANDLER_CATCH_EXCEPTION** - обработчик для исключения. Вызывается, если при обработке платежа было брошено исключение. **Внимание!** Если этот обработчик не установлен, то исключение будет брошено из библиотеки выше в сервис мерчанта. Принимает следующие параметры:
    * Исключение
    * Платеж
    * Ответ от PaynetEasy (опционально, не доступен, если произошла ошибка на этапе формирования или выполнения запроса к PaynetEasy)

Метод принимает один параметр:
* Массив с обработчиками событий. Ключами элементов массива являются названия обработчиков, заданные в константах класса, значениями - любые значения типа [callable](http://php.net/manual/en/language.types.callable.php)

Пример вызова метода с простейшими обработчиками:

```php
use PaynetEasy\PaynetEasyApi\PaymentProcessor;
use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\Transport\Response;
use Exception;

$paymentProcessor = new PaymentProcessor;
$paymentProcessor->setHandlers(array
(
    PaymentProcessor::HANDLER_SAVE_PAYMENT      => function(Payment $payment)
    {
        start_session();
        $_SESSION['payment'] = serialize($payment);
    },
    PaymentProcessor::HANDLER_STATUS_UPDATE     => function()
    {
        header("Location: http://{$_SERVER['HTTP_HOST']}/{$_SERVER['REQUEST_URI']}");
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
    PaymentProcessor::HANDLER_FINISH_PROCESSING => function(Payment $payment)
    {
        print "<pre>";
        print_r("Payment state: {$payment->getProcessingStage()}\n");
        print_r("Payment status: {$payment->getStatus()}\n");
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