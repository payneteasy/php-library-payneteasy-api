# Data validator, Validator

Static class **[PaynetEasy\PaynetEasyApi\Util\Validator](../../../source/PaynetEasy/PaynetEasyApi/Util/Validator.php)** provides the following data validation methods:
* **[validateByRule()](#validateByRule)**: validation using a predefined rule or a regular expression

### <a name="validateByRule"></a>validateByRule(): validation using a predefined rule

For convenient data validation,**[Validator](../../../source/PaynetEasy/PaynetEasyApi/Util/Validator.php)** has **[validateByRule()](../../../source/PaynetEasy/PaynetEasyApi/Util/Validator.php#L128)** method and a set of constants with validation rules. Available rules follow:

Constant                        |Validation rule            |Description
--------------------------------|---------------------------|--------
Validator::EMAIL                |[FILTER_VALIDATE_EMAIL](http://www.php.net/manual/en/filter.filters.validate.php)|Validate value as email
Validator::IP                   |[FILTER_VALIDATE_IP](http://www.php.net/manual/en/filter.filters.validate.php)|Validate value as IP address
Validator::URL                  |[FILTER_VALIDATE_URL](http://www.php.net/manual/en/filter.filters.validate.php)|Validate value as URL
Validator::MONTH                |in_array($month, range(1, 12))|Validate value as month
Validator::YEAR                 |#^[0-9]{1,2}$#i            |Validate value as year
Validator::PHONE                |#^[0-9\-\+\(\)\s]{6,15}$#i |Validate value as phone number
Validator::AMOUNT               |#^[0-9\.]{1,11}$#i         |Validate value as payment amount
Validator::CURRENCY             |#^[A-Z]{1,3}$#i            |Validate value as currency
Validator::CVV2                 |#^[\S\s]{3,4}$#i           |Validate value as card verification value
Validator::ZIP_CODE             |#^[\S\s]{1,10}$#i          |Validate value as zip code
Validator::COUNTRY              |#^[A-Z]{1,2}$#i            |Validate value as two-letter country or state code
Validator::DATE                 |#^[0-9]{6}$#i              |Validate value as date in format MMDDYY
Validator::SSN                  |#^[0-9]{1,4}$#i            |Validate value as last four digits of social security number
Validator::CREDIT_CARD_NUMBER   |#^[0-9]{1,20}$#i           |Validate value as credit card number
Validator::ID                   |#^[\S\s]{1,20}$#i          |Validate value as ID (client, paynet, card-ref, etc.)
Validator::LONG_STRING          |#^[\S\s]{1,128}$#i         |Validate value as long string
Validator::MEDIUM_STRING        |#^[\S\s]{1,50}$#i          |Validate value as medium string

Method accepts three parameters:
* Value to be validated
* Rule name or regular expression to validate
* Flag which defines method behavior in case when validation has failed
    * **true** - an exception will be thrown
    * **false** - boolean validation result will be returned
Method usage example:

```php
use PaynetEasy\PaynetEasyApi\Util\Validator;
use PaynetEasy\PaynetEasyApi\Exception\ValidationException;

var_dump(Validator::validateByRule('test@mail.com', Validator::EMAIL, false));  // true
var_dump(Validator::validateByRule('some string', '#\d#', false));              // false

// prints 'invalid'
try
{
    Validator::validateByRule('test[at]mail.com', Validator::EMAIL);
    print 'valid';
}
catch (ValidationException $e)
{
    print 'invalid';
}
```
