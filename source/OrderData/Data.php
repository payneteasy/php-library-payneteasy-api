<?PHP
namespace PaynetEasy\Paynet\OrderData;

use ArrayObject;
use PaynetEasy\Paynet\Exception\ValidationException;

class Data extends ArrayObject
{
    protected $properties       = array();
    protected $validate_preg    = array();

    public function __construct($array = array())
    {
        parent::__construct($array);
    }

    /**
     * Validate Customer Data.
     *
     * @throws ValidationException
     */
    public function validate()
    {
        $this->checkRequired();

        $this->validatePreg();
    }

    /**
     * Returned Data for query.
     *
     * @return      array
     */
    public function getData()
    {
        $this->validate();

        return $this->getArrayCopy();
    }

    protected function checkRequired()
    {
        $missedFields = array();

        foreach ($this->properties as $fieldName => $isFieldRequired)
        {
            if(   $isFieldRequired
               && empty($this[$fieldName]))
            {
                $missedFields[] = $fieldName;
            }
        }

        if(count($missedFields))
        {
            throw new ValidationException('Some required fields missed or empty : ' .
                                          implode(', ', $missedFields));
        }
    }

    protected function validatePreg()
    {
        $errors = array();

        foreach($this->validate_preg as $fieldName  => $regExp)
        {
            if(    $this->offsetExists($fieldName)
               && !preg_match($regExp, $this[$fieldName]))
            {
                $errors[$fieldName] = "{$fieldName} does not match {$regExp}";
            }
        }

        if(count($errors))
        {
            throw new ValidationException('Some fields has invalid value: ' .
                                          implode(', ', $errors));
        }
    }

    protected function getValue($key)
    {
        if($this->offsetExists($key))
        {
            return $this->offsetGet($key);
        }
    }

    /**
     * Validates value by regExp.
     *
     * @param       string      $value              Value for validation
     * @param       string      $regExp             Regular expression for validation
     * @param       string      $errorMessage       Error message
     *
     * @throws      ValidationException             Value does not match regexp
     */
    protected function validateValue($value, $regExp, $errorMessage = '')
    {
        if (filter_var($value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $regExp))))
        {
            return;
        }

        if (empty($errorMessage))
        {
            $errorMessage = "Value '{$value}' does not match the regexp '{$regExp}'";
        }

        throw new ValidationException($errorMessage);
    }
}