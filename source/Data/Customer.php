<?PHP
namespace PaynetEasy\Paynet\Data;

/**
 * Container for customer data
 *
 */
class Customer    extends     Data
{
    public function __construct($array)
    {
        $this->properties = array
        (
            'first_name'    => false,
            'last_name'     => false,
            'email'         => true,
            'address'       => false,
            'birthday'      => false,
            'city'          => true,
            'state'         => false,
            'zip_code'      => true,
            'country'       => true,
            'phone'         => true,
            'cell_phone'    => false,
            'ssn'           => false
        );

        $this->validate_preg = array
        (
            'first_name'    => '|^[^0-9]{2,50}$|i',
            'last_name'     => '|^[^0-9]{2,50}$|i',
            'address'       => '|^[\S\s]{2,50}$|i',
            'country'       => '|^[A-Z]{1,2}$|i',
            'state'         => '|^[A-Z]{1,2}$|i',
            'city'          => '|^[\S\s]{2,50}$|i',
            'zip_code'      => '|^[\S\s]{1,10}$|i',
            'phone'         => '|^[0-9\-\+\(\)]{6,15}$|i',
            'cell_phone'    => '|^[0-9\-\+\(\)]{6,15}$|i',
            'email'         => '/^[\.\-_A-Za-z0-9]+?@[\.\-A-Za-z0-9]+?\.[A-Za-z0-9]{2,6}$/',
            'birthday'      => '|^[0-9]{6}$|i',
            'ssn'           => '|^[0-9]{1,4}$|i'
        );

        parent::__construct($array);
    }

    public function getEmail()
    {
        return $this->get_value('email');
    }

    /**
     * Returned Data for query.
     *
     * @return      array
     */
    public function getData()
    {
        $result             = parent::getData();
        $result['address1'] = $result['address'];
        unset($result['address']);

        return $result;
    }
}