# Валидатор данных, Validator

Статический класс **[PaynetEasy\PaynetEasyApi\Utils\Validator](../../source/PaynetEasy/PaynetEasyApi/Utils/Validator.php)** предоставляет следующие методы для валидации данных:
* **[validateByRule()](#validateByRule)**: валидация с помощью предопределенного правила или регулярного выражения
* **[validateByRegExp()](#validateByRegExp)**: валидация с помощью регулярного выражения

### <a name="validateByRule"></a>validateByRule(): валидация с помощью предопределнного правила

Для удобной валидации данных в **[Validator](../../source/PaynetEasy/PaynetEasyApi/Utils/Validator.php)** реализован метод **[validateByRule()](../../source/PaynetEasy/PaynetEasyApi/Utils/Validator.php#L126)** и набор констант с правилами валидации. Список доступных правил:

Константа                       |Правило валидации          |Описание
--------------------------------|---------------------------|--------
Validator::EMAIL                |[FILTER_VALIDATE_EMAIL](http://www.php.net/manual/en/filter.filters.validate.php)|Validate value as email
Validator::IP                   |[FILTER_VALIDATE_IP](http://www.php.net/manual/en/filter.filters.validate.php)|Validate value as IP address
Validator::URL                  |[FILTER_VALIDATE_URL](http://www.php.net/manual/en/filter.filters.validate.php)|Validate value as URL
Validator::MONTH                |0 < month < 13             |Validate value as month
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

Метод принимает три параметра:
* Значение для валидации
* Имя правила или регулярное выражение для валидации
* Флаг, определяющий поведение метода в том случае, если значение не прошло валидацию
    * **true** - будет брошено исключение
    * **false** - будет возвращен булевый результат проверки
Пример использования метода:

```php
use PaynetEasy\PaynetEasyApi\Utils\Validator;
use PaynetEasy\PaynetEasyApi\Exception\ValidationException;

var_dump(Validator::validateByRule('test@mail.com', Validator::EMAIL, false));  // true
var_dump(Validator::validateByRule('some string', '#\d#', false));              // false

// prints 'invalid'
try
{
    Validator::validateByRule('test[at]mail.com', Validator::EMAIL));
    print 'valid';
}
catch (ValidationException $e)
{
    print 'invalid';
}
```

### <a name="validateByRegExp"></a>validateByRegExp(): валидация с помощью регулярного выражения

Метод принимает три параметра:
* Значение для валидации
* Регулярное выражение для валидации
* Флаг, определяющий поведение метода в том случае, если значение не прошло валидацию
    * **true** - будет брошено исключение
    * **false** - будет возвращен булевый результат проверки
Пример использования метода:

```php
use PaynetEasy\PaynetEasyApi\Utils\Validator;
use PaynetEasy\PaynetEasyApi\Exception\ValidationException;

var_dump(Validator::validateByRule('test@mail.com', '#\w+@mail\.com#', false));  // true
var_dump(Validator::validateByRule('some string', '#\d#', false));              // false

// prints 'invalid'
try
{
    Validator::validateByRule('test@test.com', '#\w+@mail\.com#'));
    print 'valid';
}
catch (ValidationException $e)
{
    print 'invalid';
}
```
