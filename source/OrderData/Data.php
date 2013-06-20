<?PHP
namespace PaynetEasy\Paynet\OrderData;

use PaynetEasy\Paynet\Utils\String;
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

    protected function getGetterByProperty($propertyName)
    {
        return 'get' . ucfirst($propertyName);
    }
}