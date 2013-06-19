<?PHP
namespace PaynetEasy\Paynet\OrderData;

use ArrayObject;
use PaynetEasy\Paynet\Exception\PaynetException;
use RuntimeException;

class Data extends ArrayObject
{
    protected $properties       = array();
    protected $validate_preg    = array();
    protected $errors           = array();

    public function __construct($array = array())
    {
        parent::__construct($array);
    }

    /**
     * Validate Customer Data.
     *
     * @throws PaynetException
     */
    public function validate()
    {
        $this->checkRequired();

        $this->validatePreg();
    }

    /**
     * Return validating errors.
     * @return      array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Returned Data for query.
     *
     * @return      array
     */
    public function getData()
    {
        if(count($this->errors))
        {
            throw new PaynetException(__METHOD__.' failed: ' . implode(', ', $this->errors));
        }

        return $this->getArrayCopy();
    }

    protected function checkRequired()
    {
        foreach ($this->properties as $propertyName => $isPropertyRequired)
        {
            if($isPropertyRequired && empty($this[$propertyName]))
            {
                $this->errors[] = "{$propertyName} is required!";
            }
        }

        if(count($this->errors))
        {
            throw new PaynetException(__METHOD__.' failed: ' . implode(', ', $this->errors));
        }
    }

    protected function validatePreg()
    {
        foreach($this->validate_preg as $property  => $preg)
        {
            if($this->offsetExists($property)
            && !preg_match($preg, $this[$property])
            )
            {
                $this->errors[$property] = '%s is incorrect';
            }
        }

        if(count($this->errors))
        {
            throw new PaynetException(__METHOD__.' failed: ' . implode(', ', $this->errors));
        }
    }

    protected function getValue($key)
    {
        if($this->offsetExists($key))
        {
            return $this->offsetGet($key);
        }
        else
        {
            return '';
        }
    }

    /**
     * Validates value by regExp.
     *
     * @param       string      $value              Value for validation
     * @param       string      $regExp             Regular expression for validation
     * @param       string      $errorMessage       Error message
     *
     * @throws      RuntimeException        Value does not match regexp
     */
    protected function validateValue($value, $regExp, $errorMessage = '')
    {
        if (!filter_var($value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $regExp))))
        {
            if (empty($errorMessage))
            {
                $errorMessage = "Value '{$value}' does not match the regexp '{$regExp}'";
            }

            throw new RuntimeException($errorMessage);
        }
    }
}