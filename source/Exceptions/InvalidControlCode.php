<?PHP
namespace PaynetEasy\Paynet\Exceptions;

class InvalidControlCode extends PaynetException
{
    public function __construct($control, $expected_control)
    {
        parent::__construct("Control code invalid: '$control' !== expected: '$expected_control'");
    }
}