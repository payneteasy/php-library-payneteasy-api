# Класс для работы с цепочками свойств, PropertyAccessor

В процессе работы с данными платежа возникает необходимость читать и изменять свойства объектов, хранящихся в Payment. Например, для чтения email клиента необходимо вызвать `$payment->getCustomer()->getEmail()`, а для записи - `$payment->getCustomer()->setEmail()`. Для удобного выполнения этих операций в классе **[PaynetEasy\PaynetEasyApi\Utils\PropertyAccessor](../../source/PaynetEasy/PaynetEasyApi/Utils/PropertyAccessor.php)** реализованы следующие методы:
* **[getValue()](#getValue)**: удобное чтение данных по цепочке свойств
* **[setValue()](#setValue)**: удобная запись данных по цепочке свойств

### <a name="getValue"></a> getValue(): удобное чтение данных по цепочке свойств

Метод предназначен для чтения данных из цепочки свойств. Цепочка свойств описывает порядок получения свойств из переданного объекта. Так, для цепочки `billingAddress.firstLine` будет получено значение свойства `firstLine` из объекта, хранящегося в свойстве `billingAddress`. Для получения свойств используются методы-геттеры, названия которых образованы добавлением префикса `get` к имени свойства. Таким образом, получение данных для цепочки `billingAddress.firstLine` приведет к вызову кода `$payment->getBillingAddress()->getFirstLine()`.
Метод принимает три параметра:
* Объект, цепочку свойств которого можно прочитать
* Цепочка свойств
* Флаг, определяющий поведение метода в том случае, если геттер для свойства не был найден или если свойство, в котором ожидался объект, пустое:
    * **true** - будет брошено исключение
    * **false** - будет возвращен `null`

Пример использования метода:
```php
use PaynetEasy\PaynetEasyApi\Utils\PropertyAccessor;
use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\PaymentData\CreditCard;
use RuntimeException;

$creditCard = (new CreditCard)->setExpireYear(2014);
$payment    = (new Payment)->setCreditCard($creditCard);

var_dump(PropertyAccessor::getValue($payment, 'creditCard.expireYear')); // 2014
var_dump(PropertyAccessor::getValue($payment, 'creditCard.expireMonth', false)); // null

// prints 'empty'
try
{
    PropertyAccessor::getValue($payment, 'creditCard.expireMonth');
}
catch (RuntimeException $e)
{
    print 'empty';
}
```

### <a name="setValue"></a> setValue(): удобное изменение данных по цепочке свойств

Метод предназначен для изменения данных по цепочке свойств. Цепочка свойств описывает порядок получения свойств из переданного объекта. Так, для цепочки `billingAddress.firstLine` будет изменено значение свойства `firstLine` из объекта, хранящегося в свойстве `billingAddress`. Для получения свойств используются методы-геттеры, названия которых образованы добавлением префикса `get` к имени свойства, для изменения - методы-сеттеры, названия которых образованы добавлением префикса `set` к имени свойства. Таким образом, изменение данных для цепочки `billingAddress.firstLine` приведет к вызову кода `$payment->getBillingAddress()->setFirstLine($firstLine)`.
Метод принимает четыре параметра:
* Объект, цепочку свойств которого можно прочитать
* Цепочка свойств
* Новое значение
* Флаг, определяющий поведение метода в том случае, если геттер или сеттер для свойства не был найден или если свойство, в котором ожидался объект, пустое:
    * **true** - будет брошено исключение
    * **false** - будет возвращен `null`

Пример использования метода:
```php
use PaynetEasy\PaynetEasyApi\Utils\PropertyAccessor;
use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\PaymentData\CreditCard;
use RuntimeException;

$creditCard = (new CreditCard)->setExpireYear(2014);
$payment    = (new Payment)->setCreditCard($creditCard);

PropertyAccessor::setValue($payment, 'creditCard.expireYear', 2015);
var_dump(PropertyAccessor::getValue($payment, 'creditCard.expireYear')); // 2015

PropertyAccessor::setValue($payment, 'creditCard.nonExistentProperty', 'value', false); // nothing will happen

// prints 'nonexistent property'
try
{
    PropertyAccessor::setValue($payment, 'creditCard.nonExistentProperty', 'value');
}
catch (RuntimeException $e)
{
    print 'nonexistent property';
}
```
