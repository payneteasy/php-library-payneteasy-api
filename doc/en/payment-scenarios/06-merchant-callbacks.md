# Merchant callbacks

## General provisions

* This article only describes library usage. Full information on Merchant callbacks may be found at [PaynetEasy documentation section](http://doc.payneteasy.com/doc/merchant-callbacks.htm).
* Property chains usage may be found in **[PropertyAccessor](../library-internals/03-property-accessor.md)** class description

## <a name="main-callbacks"></a> Sale, Return, Chargeback callbacks

After the payment has been processed by PaynetEasy, processing result data is sent to the merchant service using a callback. This is done to allow payment to be processed by merchant service regardless of whether user was correctly redirected from PaynetEasy gateway or not. Processing of this data is the same as processing for [sale-form, preauth-form and transfer-form](05-payment-form-integration.md) flows, it is described in the [basic library usage example](../00-basic-tutorial.md#stage_2).
When processing the payment, the library validates presence of the required fields in callback data first, and then it matches some fields of the payment to fields of the callback. After these validations, **status** field is updated in the payment object.

##### Mandatory callback parameters

Request field       |Payment property chain to match
--------------------|---------------------------------------
orderid             |payment.paynetId
merchant_order      |payment.clientId
client_orderid      |payment.clientId
amount              |payment.amount
status              |
type                |
control             |

* [sale request execution example](../../../example/sale.php#L107)
* [preauth request execution example](../../../example/preauth.php#L107)
* [sale-form request execution example](../../../example/sale-form.php#L86)
* [preauth-form request execution example](../../../example/preauth-form.php#86)
* [transfer-form request execution example](../../../example/transfer-form.php#86)
