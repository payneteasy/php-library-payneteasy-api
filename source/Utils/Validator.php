<?php

namespace PaynetEasy\Paynet\Utils;

use PaynetEasy\Paynet\Exception\ValidationException;

class Validator
{
    /**
     * Validate value as email
     */
    const EMAIL = FILTER_VALIDATE_EMAIL;

    /**
     * Validate value as IP address
     */
    const IP    = FILTER_VALIDATE_IP;

    /**
     * Validate value as URL
     */
    const URL   = FILTER_VALIDATE_URL;

    /**
     * Validate value as month
     */
    const MONTH = 'month';

    /**
     * Allowed rules list
     *
     * @var     array
     */
    static protected $allowedRules = array
    (
        self::EMAIL,
        self::IP,
        self::URL,
        self::MONTH
    );

    /**
     * Validates value by given rule.
     * Rule can be one of Validator constants or regExp.
     *
     * @param       string      $value              Value for validation
     * @param       string      $rule               Rule for validation
     *
     * @throws      ValidationException             Value does not match rule
     */
    static public function validateByRule($value, $rule, $failOnInvalidValue = true)
    {
        $valid = false;

        switch ($rule)
        {
            case self::EMAIL:
            case self::IP:
            case self::URL:
            {
                $valid = filter_var($value, $rule);
                break;
            }
            case self::MONTH:
            {
                $valid = ($value >= 1 && $value <= 12);
                break;
            }
            default:
            {
                $valid = filter_var($value, FILTER_VALIDATE_REGEXP,
                                    array('options' => array('regexp' => $rule)));
            }
        }

        if ($valid !== false)
        {
            return true;
        }
        elseif ($failOnInvalidValue === true)
        {
            throw new ValidationException("Value '{$value}' does not match rule '{$rule}'");
        }
        else
        {
            return false;
        }
    }
}