<?PHP
namespace PaynetEasy\Paynet\OrderData;

use PaynetEasy\Paynet\Utils\String;
use PaynetEasy\Paynet\Exception\ValidationException;
use RuntimeException;

class Data
{
    /**
     * Initialize object from input array.
     * For each array field will be called property setter.
     * If the setter is not found, and $failOnUnknownField = true,
     * exception will be trown.
     *
     * :NOTICE:         Imenem          20.06.13
     *
     * Every child class can have own logic
     * to find setter by array field name
     *
     * @param       array       $array                  Input array with object data
     * @param       boolean     $failOnUnknownField     If true, exception will be trown if the setter is not found
     *
     * @throws      RuntimeException                    Setter is not found
     */
    public function __construct($array = array(), $failOnUnknownField = true)
    {
        foreach ($array as $fieldName => $fieldValue)
        {
            $setter = array($this, $this->getSetterByField($fieldName));

            if (is_callable($setter))
            {
                call_user_func($setter, $fieldValue);
            }
            elseif ($failOnUnknownField === true)
            {
                throw new RuntimeException("Can not find setter for field '{$fieldName}'");
            }
        }
    }

    /**
     * Get all object scalar properties values as array.
     *
     * :NOTICE:         Imenem          20.06.13
     *
     * Every child class can have own logic
     * to find array field name by property name
     *
     * @param       boolean     $failOnUnknownField     If true, exception will be trown if the setter is not found
     *
     * @return      array                               Array with all object scalar properties values
     */
    public function getData($failOnUnknownField = true)
    {
        $data = array();

        foreach ($this as $propertyName => $propertyValue)
        {
            if (!is_scalar($propertyValue))
            {
                continue;
            }

            $getter = array($this, $this->getGetterByProperty($propertyName));

            if (is_callable($getter))
            {
                $data[$this->getFieldByProperty($propertyName)] = call_user_func($getter);
            }
            elseif ($failOnUnknownField === true)
            {
                throw new RuntimeException("Can not find getter for property '{$propertyName}'");
            }
        }

        return $data;
    }

    /**
     * Get property setter name by input array field name
     *
     * @param       string      $fieldName          Input array field name
     *
     * @return      string                          Property setter name
     */
    protected function getSetterByField($fieldName)
    {
        return 'set' . String::camelize($fieldName);
    }

    /**
     * Get output array field name by property name
     *
     * @param       string      $propertyName       Property name
     *
     * @return      string                          Output array field name
     */
    protected function getFieldByProperty($propertyName)
    {
        return String::uncamelize($propertyName);
    }

    protected function getGetterByProperty($propertyName)
    {
        return 'get' . ucfirst($propertyName);
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