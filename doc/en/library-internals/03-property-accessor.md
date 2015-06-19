# Property chains utility class, PropertyAccessor

When working with payment data, a need arises to get and set properties of objects embedded into PaymentTransaction. For example, to get client's email we need to call `$paymentTransaction->getPayment()->getCustomer()->getEmail()`, and to set it - `$paymentTransaction->getPayment()->getCustomer()->setEmail()`. For convenience, **[PaynetEasy\PaynetEasyApi\Util\PropertyAccessor](../../source/PaynetEasy/PaynetEasyApi/Util/PropertyAccessor.php)** class contains the following methods:
* **[getValue()](#getValue)**: convenient data reading using property chain
* **[setValue()](#setValue)**: convenient data writing using property chain

### <a name="getValue"></a> getValue(): convenient data reading using property chain

Method is for reading data from property chain. A property chain describes property path for the given object. For instance, for `payment.billingAddress.firstLine` chain, `firstLine` property value will be taken from object stored in `billingAddress` property stored in `payment` property. To read property values, getter methods are used, names of which are constructed by prepending `get` prefix to property name. So, reading data with `payment.billingAddress.firstLine` chain will call `$paymentTransaction->getPayment()->getBillingAddress()->getFirstLine()`.
Method accepts three parameters:
* Object to which property chain needs to be applied for reading
* Property chain
* Flag which defines behavior in case when getter corresponding to property was not found, or when property which is going to be dereferenced is null:
    * **true** - an exception will be thrown
    * **false** - `null` will be returned

Method usage example:
```php
use PaynetEasy\PaynetEasyApi\Util\PropertyAccessor;
use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\PaymentData\CreditCard;
use RuntimeException;

$paymentTransaction = new PaymentTransaction(array
(
    'payment'               => new Payment(array
    (
        'credit_card'           =>  new CreditCard(array
        (
            'expire_year'           => '14'
        ))
    )),
));

var_dump(PropertyAccessor::getValue($paymentTransaction, 'payment.creditCard.expireYear')); // 14
var_dump(PropertyAccessor::getValue($paymentTransaction, 'payment.creditCard.expireMonth', false)); // null

// prints 'empty'
try
{
    PropertyAccessor::getValue($paymentTransaction, 'payment.creditCard.expireMonth');
}
catch (RuntimeException $e)
{
    print 'empty';
}
```

### <a name="setValue"></a> setValue(): convenient data writing using property chain

Method is for writing data to property chain. A property chain describes property path for the given object. For instance, for `payment.billingAddress.firstLine` chain, `firstLine` property value will be changed in object stored in `billingAddress` property stored in `payment` property. To read property values, getter methods are used, names of which are constructed by prepending `get` prefix to property name; for changing setter methods are used which use `set` prefix. So, writing data with `payment.billingAddress.firstLine` chain will call `$paymentTransaction->getPayment()->getBillingAddress()->setFirstLine($firstLine)`.
Method accepts four parameters:
* Object to which property chain needs to be applied for writing
* Property chain
* Value to write
* Flag which defines behavior in case when getter or setter corresponding to property was not found, or when property which is going to be dereferenced is null:
    * **true** - an exception will be thrown
    * **false** - `null` will be returned

Method usage example:
```php
use PaynetEasy\PaynetEasyApi\Util\PropertyAccessor;
use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\PaymentData\CreditCard;
use RuntimeException;

$paymentTransaction = new PaymentTransaction(array
(
    'payment'               => new Payment(array
    (
        'credit_card'           =>  new CreditCard(array
        (
            'expire_year'           => '14'
        ))
    )),
));

PropertyAccessor::setValue($paymentTransaction, 'payment.creditCard.expireYear', 15);
var_dump(PropertyAccessor::getValue($paymentTransaction, 'payment.creditCard.expireYear')); // 15

PropertyAccessor::setValue($paymentTransaction, 'payment.creditCard.nonExistentProperty', 'value', false); // nothing will happen

// prints 'nonexistent property'
try
{
    PropertyAccessor::setValue($paymentTransaction, 'payment.creditCard.nonExistentProperty', 'value');
}
catch (RuntimeException $e)
{
    print 'nonexistent property';
}
```
