<?PHP
namespace PaynetEasy\Paynet\Exception;

class InvalidControlCodeException extends PaynetException
{
    public function __construct($control, $expected_control)
    {
        parent::__construct("Control code invalid: '$control' !== expected: '$expected_control'");
    }
}