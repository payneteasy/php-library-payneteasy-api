<?PHP
namespace PaynetEasy\Paynet\Exceptions;

use \Exception;

class PaynetException extends Exception
{
    protected $errors = array();

    public function __construct($message, $code = 0, $previous = null)
    {
        if(is_array($code))
        {
            $this->errors       = $code;
            $code               = 0;
        }

        parent::__construct($message, $code, $previous);
    }

    public function getErrors()
    {
        return $this->errors;
    }

}