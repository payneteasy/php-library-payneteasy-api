<?PHP
namespace PaynetEasy\Paynet\Data;

use \ArrayObject;
use \PaynetEasy\Paynet\Exceptions\PaynetException;

class Data    extends     ArrayObject
{
    protected $properties;

    protected $validate_preg;

    protected $errors       = array();

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
        $this->check_the_required();

        $this->validate_preg();
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
            throw new PaynetException(__CLASS__.'::'.__METHOD__.' failed', $this->errors);
        }

        return $this->getArrayCopy();
    }

    protected function check_the_required()
    {
        foreach ($this->properties as $k => $v)
        {
            if($v && empty($this[$k]))
            {
                $this->errors[$k]       = '%s is required!';
            }
        }

        if(count($this->errors))
        {
            throw new PaynetException(__CLASS__.'::'.__METHOD__.' failed', $this->errors);
        }
    }

    protected function validate_preg()
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
            throw new PaynetException(__CLASS__.'::'.__METHOD__.' failed', $this->errors);
        }
    }

    protected function get_value($key)
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
}