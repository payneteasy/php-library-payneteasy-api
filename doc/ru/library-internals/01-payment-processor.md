# Фронтенд библиотеки, PaymentProcessor

Фронтенд библиотеки представлен классом **[PaynetEasy\PaynetEasyApi\PaymentProcessor](../../../source/PaynetEasy/PaynetEasyApi/PaymentProcessor.php)**. Класс предоставляет следующие методы:
* **[executeQuery()](#executeQuery)**: выполнение запроса к PaynetEasy
* **[processCustomerReturn()](#processCustomerReturn)**: обработка данных, полученных от PaynetEasy при возвращении пользователя с платежного шлюза
* **[processPaynetEasyCallback()](#processPaynetEasyCallback)**: обработка данных, полученных от PaynetEasy при поступлении коллбэка от PaynetEasy
* **[setHandlers()](#setHandlers)**: установка обработчиков для различных событий, происходящих при обработке платежной транзакции

### <a name="executeQuery"></a>executeQuery(): выполнение запроса к PaynetEasy

Некоторые сценарии обработки платежа имеют асинхронную природу и состоят из нескольких запросов. Так, некоторые запросы не возвращают результат платежа сразу и требуют многократного выполнения запроса **status**, после которого клиент может быть отправлен на шлюз PaynetEasy для проведения дополнительных шагов авторизации. После возвращения клиента на сервис мерчанта необходима обработка данных, полученных от шлюза.
<a name="async_queries_list"></a>Cписок асинхронных запросов:
* sale
* preauth
* capture
* return
* make-rebill
* transfer-by-ref

Ознакомиться с обработкой таких запросов можно в следующих файлах:
* [Пример выполнения запроса sale](../../../example/sale.php)
* [Пример выполнения запроса preauth](../../../example/preauth.php)
* [Пример выполнения запроса capture](../../../example/capture.php)
* [Пример выполнения запроса return](../../../example/return.php)
* [Пример выполнения запроса make-rebill](../../../example/make-rebill.php)
* [Пример выполнения запроса transfer-by-ref](../../../example/transfer-by-ref.php)

Отдельный сценарий обработки необходим и при интеграции платежной формы. Запрос к шлюзу возвращает ссылку на платежную форму, на которую должен быть отправлен клиент. После заполнения и отправки данных шлюз обрабатывает платежную форму и возвращает клиента на сервис мерчанта. После возвращения клиента на сервис мерчанта необходима обработка данных, полученных от шлюза.
<a name="form_queries_list"></a>Список запросов для интеграции платежной формы:
* sale-form
* preauth-form
* transfer-form

Ознакомиться с обработкой таких запросов можно в следующих файлах:
* [Пример выполнения запроса sale-form](../../../example/sale-form.php)
* [Пример выполнения запроса preauth-form](../../../example/preauth-form.php)
* [Пример выполнения запроса transfer-form](../../../example/transfer-form.php)

Некоторые операции с платежами не требуют сложных сценариев обработки и выполняются с помощью одного запроса.
Список простых операций над платежом:
* create-card-ref
* get-card-info
* status

Ознакомиться с обработкой таких запросов можно в следующих файлах:
* [Пример выполнения запроса create-card-ref](../../../example/create-card-ref.php)
* [Пример выполнения запроса get-card-info](../../../example/get-card-info.php)
* [Пример выполнения запроса status](../../../example/status.php)

Для удобного выполнения запросов к PaynetEasy в **PaymentProcessor** реализован метод **[executeQuery()](../../../source/PaynetEasy/PaynetEasyApi/PaymentProcessor.php#L114)**.
Метод принимает два параметра:
* Название запроса
* Платежная транзакция для обработки

### <a name="processCustomerReturn"></a>processCustomerReturn(): обработка данных, полученных от PaynetEasy при возвращении клиента

Каждый [асинхронный запрос](#async_queries_list) может завершиться перенаправлением пользователя на платежный шлюз для выполнения дополнительных действий, а каждый [запрос для интеграции платежной формы](#form_queries_list) обязательно содержит такое перенаправление. Каждый раз при возвращении пользователя на сервис мерчанта передаются данные с результатом обработки платежа. Также, если в [конфигурации стартового запроса](../00-basic-tutorial.md#stage_1_step_3) был задан ключ **server_callback_url**, то через некоторое время PaynetEasy вызовет этот url и передаст ему данные, описанные в wiki PaynetEasy в разделе [Merchant Callbacks](http://wiki.payneteasy.com/index.php/PnE:Merchant_Callbacks). Для удобной обработки этих данных в **PaymentProcessor** реализован метод **[processCustomerReturn()](../../../source/PaynetEasy/PaynetEasyApi/PaymentProcessor.php#L144)**.
Метод принимает два параметра:
* Объект с данными, полученными при возвращении пользователя от PaynetEasy
* Платежная транзакция для обработки

Ознакомиться с использованием данного метода можно в следующих файлах:
* [Базовый пример использования библиотеки](../00-basic-tutorial.md#stage_2)
* [Пример выполнения запроса sale](../../../example/sale.php#L96)
* [Пример выполнения запроса preauth](../../../example/preauth.php#L96)
* [Пример выполнения запроса sale-form](../../../example/sale-form.php#L75)
* [Пример выполнения запроса preauth-form](../../../example/preauth-form.php#L75)
* [Пример выполнения запроса transfer-form](../../../example/transfer-form.php#L75)

### <a name="processPaynetEasyCallback"></a>processPaynetEasyCallback(): обработка удаленного вызова от PaynetEasy

После выполнения [асинхронного запроса](#async_queries_list) или [запроса для интеграции платежной формы](#form_queries_list), если в [конфигурации стартового запроса](../00-basic-tutorial.md#stage_1_step_3) был задан ключ **server_callback_url**, то через некоторое время PaynetEasy вызовет этот url и передаст ему данные, описанные в wiki PaynetEasy в разделе [Merchant Callbacks](http://wiki.payneteasy.com/index.php/PnE:Merchant_Callbacks). Для удобной обработки этих данных в **PaymentProcessor** реализован метод **[processPaynetEasyCallback()](../../../source/PaynetEasy/PaynetEasyApi/PaymentProcessor.php#L159)**.
Метод принимает два параметра:
* Объект с данными, полученными при возвращении пользователя от PaynetEasy
* Платежная транзакция для обработки

Ознакомиться с использованием данного метода можно в следующих файлах:
* [Пример выполнения запроса sale](../../../example/sale.php#L107)
* [Пример выполнения запроса preauth](../../../example/preauth.php#L107)
* [Пример выполнения запроса sale-form](../../../example/sale-form.php#L86)
* [Пример выполнения запроса preauth-form](../../../example/preauth-form.php#L86)
* [Пример выполнения запроса transfer-form](../../../example/transfer-form.php#L86)

### <a name="setHandlers"></a> setHandlers(): установка обработчиков для различных событий, происходящих при обработке заказа

**PaymentProcessor** скрывает от конечного пользователя алгоритм обработки заказа в методах **[executeQuery()](#executeQuery)**, **[processCustomerReturn()](#processCustomerReturn)** и **[processPaynetEasyCallback()](#processPaynetEasyCallback)**. При этом во время обработки заказа возникают ситуации, обработка которых должна быть реализована на стороне сервиса мерчанта. Для обработки таких ситуаций в **PaymentProcessor** реализована система событий и их обработчиков. Обработчики могут быть установлены тремя разными способами:
* Передача массива c обработчиками в [конструктор класса **PaymentProcessor**](../../../source/PaynetEasy/PaynetEasyApi/PaymentProcessor.php#L101)
* Передача массива с обработчиками в метод [**setHandlers()**](../../../source/PaynetEasy/PaynetEasyApi/PaymentProcessor.php#L250)
* Установка обработчиков по одному с помощью метода **[setHandler()](../../../source/PaynetEasy/PaynetEasyApi/PaymentProcessor.php#L226)**

Список обработчиков событий:
* **HANDLER_SAVE_CHANGES** - обработчик для сохранения платежной транзакции. Вызывается, если данные платежной транзакции изменены. Должен реализовывать сохранение платежной транзакции в хранилище. Принимает следующие параметры:
    * Платежная транзакция
    * Ответ от PaynetEasy (опционально, не доступен, если произошла ошибка на этапе формирования или выполнения запроса к PaynetEasy)
* **HANDLER_STATUS_UPDATE** - обработчик для обновления статуса платежной транзакции. Вызывается, если статус платежной транзакции не изменился с момента последней проверки. Должен реализовывать запуск проверки статуса платежной транзакции. Принимает следующие параметры:
    * Ответ от PaynetEasy
    * Платежная транзакция
* **HANDLER_SHOW_HTML** - обработчик для вывода HTML-кода, полученного от PaynetEasy. Вызывается, если необходима 3D-авторизация пользователя. Должен реализовывать вывод HTML-кода из ответа от PaynetEasy в браузер клиента. Принимает следующие параметры:
    * Ответ от PaynetEasy
    * Платежная транзакция
* **HANDLER_REDIRECT** - обработчик для перенаправления клиента на платежную форму PaynetEasy. Вызывается после выполнения запроса [sale-form, preauth-form или transfer-form](../payment-scenarios/05-payment-form-integration.md). Должен реализовывать перенаправление пользователя на URL из ответа от PaynetEasy. Принимает следующие параметры:
    * Ответ от PaynetEasy
    * Платежная транзакция
* **HANDLER_FINISH_PROCESSING** - обработчик для дальнейшей обработки платежной транзакции сервисом мерчанта после завершения обработки библиотекой. Вызывается, если нет необходимости в дополнительных шагах для обработки транзакции. Принимает следующие параметры:
    * Платежная транзакция
    * Ответ от PaynetEasy (опционально, не доступен, если обработка платежной транзакции уже была завершена ранее)
* **HANDLER_CATCH_EXCEPTION** - обработчик для исключения. Вызывается, если при обработке платежной транзакции произошло исключение. **Внимание!** Если этот обработчик не установлен, то исключение будет брошено из библиотеки выше в сервис мерчанта. Принимает следующие параметры:
    * Исключение
    * Платежная транзакция
    * Ответ от PaynetEasy (опционально, не доступен, если произошла ошибка на этапе формирования или выполнения запроса к PaynetEasy)

Метод принимает один параметр:
* Массив с обработчиками событий. Ключами элементов массива являются названия обработчиков, заданные в константах класса, значениями - любые значения типа [callable](http://php.net/manual/en/language.types.callable.php)

Пример вызова метода с простейшими обработчиками:

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
