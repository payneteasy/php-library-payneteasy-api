<?php

namespace PaynetEasy\PaynetEasyApi\Utils;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-06-20 at 17:28:10.
 */
class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider testValidateByRuleProvider
     */
    public function testValidateByRule($value, $rule, $expectedResult)
    {
        $actualResult = Validator::validateByRule($value, $rule, false);
        $this->assertEquals($actualResult, $actualResult);
    }

    public function testValidateByRuleProvider()
    {
        return array
        (
            array('test@mail.com',          Validator::EMAIL,       true),
            array('test[a]mail.com',        Validator::EMAIL,       false),
            array('127.0.0.1',              Validator::IP,          true),
            array('260.0.0.1',              Validator::IP,          false),
            array('http://site.com',        Validator::URL,         true),
            array('site.com',               Validator::URL,         false),
            array('site-com',               Validator::URL,         false),
            array('1',                      Validator::MONTH,       true),
            array('12',                     Validator::MONTH,       true),
            array('13',                     Validator::MONTH,       false),
            array('0',                      Validator::MONTH,       false),
            array('str',                    Validator::MONTH,       false),
            array('str',                    Validator::MONTH,       false),
            array('US',                     Validator::COUNTRY,     true),
            array('USA',                    Validator::COUNTRY,     false),
            array('(086)543 543 54',        Validator::PHONE,       true),
            array('(086)s543b543',          Validator::PHONE,       false),
            array('0.98',                   Validator::AMOUNT,      true),
            array('98 000',                 Validator::AMOUNT,      false),
            array('USD',                    Validator::CURRENCY,    true),
            array('$',                      Validator::CURRENCY,    false),
            array('23e2derf3f4',            Validator::ID,          true)
        );
    }
}
