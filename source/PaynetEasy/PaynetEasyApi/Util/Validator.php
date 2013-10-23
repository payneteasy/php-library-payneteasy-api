<?php

namespace PaynetEasy\PaynetEasyApi\Util;

use PaynetEasy\PaynetEasyApi\Exception\ValidationException;
use PaynetEasy\PaynetEasyApi\Util\RegionFinder;

class Validator
{
    /**
     * Validate value as email
     */
    const EMAIL = 'email';

    /**
     * Validate value as IP address
     */
    const IP    = 'ip';

    /**
     * Validate value as URL
     */
    const URL   = 'url';

    /**
     * Validate value as month
     */
    const MONTH = 'month';

    /**
     * Validate value as year
     */
    const YEAR  = 'year';

    /**
     * Validate value as phone number
     */
    const PHONE = 'phone';

    /**
     * Validate value as payment amount
     */
    const AMOUNT = 'amount';

    /**
     * Validate value as currency
     */
    const CURRENCY  = 'currency';

    /**
     * Validate value as card verification value
     */
    const CVV2          = 'cvv2';

    /**
     * Validate value as zip code
     */
    const ZIP_CODE      = 'zip_code';

    /**
     * Validate value as two-letter country code
     */
    const COUNTRY       = 'country';

    /**
     * Validate value as two-letter state code
     */
    const STATE         = 'state';

    /**
     * Validate value as date in format MMDDYY
     */
    const DATE          = 'date';

    /**
     * Validate value as  last four digits of social security number
     */
    const SSN           = 'ssn';

    /**
     * Validate value as credit card number
     */
    const CREDIT_CARD_NUMBER = 'credit_card_number';

    /**
     * Validate value as different IDs (client, paynet, card-ref)
     */
    const ID        = 'id';

    /**
     * Validate value as medium string
     */
    const MEDIUM_STRING = 'medium_string';

    /**
     * Validate value as long string
     */
    const LONG_STRING   = 'long_string';

    /**
     * Regular expressions for some validation rules
     *
     * @var array
     */
    static protected $ruleRegExps = array
    (
        self::PHONE                 => '#^[0-9\-\+\(\)\s]{6,15}$#i',
        self::AMOUNT                => '#^[0-9\.]{1,11}$#i',
        self::CURRENCY              => '#^[A-Z]{1,3}$#i',
        self::CVV2                  => '#^[\S\s]{3,4}$#i',
        self::ZIP_CODE              => '#^[\S\s]{1,10}$#i',
        self::YEAR                  => '#^[0-9]{1,2}$#i',
        self::DATE                  => '#^[0-9]{6}$#i',
        self::SSN                   => '#^[0-9]{1,4}$#i',
        self::CREDIT_CARD_NUMBER    => '#^[0-9]{1,20}$#i',
        self::ID                    => '#^[\S\s]{1,20}$#i',
        self::MEDIUM_STRING         => '#^[\S\s]{1,50}$#i',
        self::LONG_STRING           => '#^[\S\s]{1,128}$#i'
    );

    /**
     * Validates value by given rule.
     * Rule can be one of Validator constants or regExp.
     *
     * @param       string      $value              Value for validation
     * @param       string      $rule               Rule for validation
     * @param       boolean     $failOnError        Throw exception on invalid value or not
     *
     * @return      boolean                         Validation result
     *
     * @throws      ValidationException             Value does not match rule (if $failOnError == true)
     */
    static public function validateByRule($value, $rule, $failOnError = true)
    {
        $valid = false;

        switch ($rule)
        {
            case self::EMAIL:
            {
                $valid = filter_var($value, FILTER_VALIDATE_EMAIL);
                break;
            }
            case self::IP:
            {
                $valid = filter_var($value, FILTER_VALIDATE_IP);
                break;
            }
            case self::URL:
            {
                $valid = filter_var($value, FILTER_VALIDATE_URL);
                break;
            }
            case self::MONTH:
            {
                $valid = in_array($value, range(1, 12));
                break;
            }
            case self::COUNTRY:
            {
                $valid = RegionFinder::hasCountryByCode($value);
                break;
            }
            case self::STATE:
            {
                $valid = RegionFinder::hasStateByCode($value);
                break;
            }
            default:
            {
                if (isset(static::$ruleRegExps[$rule]))
                {
                    $valid = static::validateByRegExp($value, static::$ruleRegExps[$rule], $failOnError);
                }
                else
                {
                    $valid = static::validateByRegExp($value, $rule, $failOnError);
                }
            }
        }

        if ($valid !== false)
        {
            return true;
        }
        elseif ($failOnError === true)
        {
            throw new ValidationException("Value '{$value}' does not match rule '{$rule}'");
        }
        else
        {
            return false;
        }
    }

    static protected function validateByRegExp($value, $regExp, $failOnError = true)
    {
        $valid = filter_var($value, FILTER_VALIDATE_REGEXP,
                            array('options' => array('regexp' => $regExp)));

        if ($valid !== false)
        {
            return true;
        }
        elseif ($failOnError === true)
        {
            throw new ValidationException("Value '{$value}' does not match regular expression '{$regExp}'");
        }
        else
        {
            return false;
        }
    }
}