<?PHP
namespace PaynetEasy\Paynet\OrderData;

use PaynetEasy\Paynet\Exception\ValidationException;

/**
 * Container for customer data
 *
 */
class       Customer
extends     Data
implements  CustomerInterface
{
    /**
     * Customer’s first name
     *
     * @var string
     */
    protected $firstName;

    /**
     * Customer’s last name
     *
     * @var string
     */
    protected $lastName;

    /**
     * Customer’s address line 1
     *
     * @var string
     */
    protected $address;

    /**
     * Customer’s city
     *
     * @var string
     */
    protected $city;

    /**
     * Customer’s state (two-letter US state code).
     * Not applicable outside the US.
     *
     * @var string
     */
    protected $state;

    /**
     * Customer’s ZIP code
     *
     * @var string
     */
    protected $zipCode;

    /**
     * Customer’s country (two-letter country code)
     *
     * @var string
     */
    protected $country;

    /**
     * Customer’s full international phone number, including country code.
     *
     * @var string
     */
    protected $phone;

    /**
     * Customer’s full international cell phone number, including country code.
     *
     * @var string
     */
    protected $cellPhone;

    /**
     * Customer’s email address
     *
     * @var string
     */
    protected $email;

    /**
     * Customer’s date of birth, in the format MMDDYY
     *
     * @var string
     */
    protected $birthday;

    /**
     * Last four digits of the customer’s social security number
     *
     * @var integer
     */
    protected $ssn;

    /**
     * {@inheritdoc}
     */
    public function setFirstName($firstName)
    {
        $this->validateValue($firstName, '#^[^0-9]{2,50}$#i');

        $this->firstName = $firstName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastName($lastName)
    {
        $this->validateValue($lastName, '#^[^0-9]{2,50}$#i');

        $this->lastName = $lastName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * {@inheritdoc}
     */
    public function setAddress($address)
    {
        $this->validateValue($address, '#^[\S\s]{2,50}$#i');

        $this->address = $address;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * {@inheritdoc}
     */
    public function setCountry($country)
    {
        $this->validateValue($country, '#^[A-Z]{1,2}$#i');

        $this->country = strtoupper($country);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * {@inheritdoc}
     */
    public function setState($state)
    {
        $this->validateValue($state, '#^[A-Z]{1,2}$#i');

        $this->state = strtoupper($state);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * {@inheritdoc}
     */
    public function setCity($city)
    {
        $this->validateValue($city, '#^[\S\s]{2,50}$#i');

        $this->city = $city;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * {@inheritdoc}
     */
    public function setZipCode($zipCode)
    {
        $this->validateValue($zipCode, '#^[\S\s]{1,10}$#i');

        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setPhone($phone)
    {
        $this->validateValue($phone, '#^[0-9\-\+\(\)\s]{6,15}$#i');

        $this->phone = $phone;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * {@inheritdoc}
     */
    public function setCellPhone($cellPhone)
    {
        $this->validateValue($cellPhone, '#^[0-9\-\+\(\)\s]{6,15}$#i');

        $this->cellPhone = $cellPhone;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCellPhone()
    {
        return $this->cellPhone;
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            throw new ValidationException("Invalid email '{$email}'");
        }

        if (strlen($email) > 50)
        {
            throw new ValidationException('Email is very long (over 50 characters)');
        }

        $this->email = $email;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function setBirthday($birthday)
    {
        $this->validateValue($birthday, '#^[0-9]{6}$#i', "Invalid birthday '{$birthday}'. " .
                                                         "Birthday format must be MMDDYY.");

        $this->birthday = $birthday;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * {@inheritdoc}
     */
    public function setSsn($ssn)
    {
        $this->validateValue($ssn, '#^[0-9]{1,4}$#i');

        $this->ssn = $ssn;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSsn()
    {
        return $this->ssn;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSetterByField($fieldName)
    {
        switch ($fieldName)
        {
            case 'address1':
            {
                return 'setAddress';
            }
            default:
            {
                return parent::getSetterByField($fieldName);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getFieldByProperty($propertyName)
    {
        switch ($propertyName)
        {
            case 'address':
            {
                return 'address1';
            }
            default:
            {
                return parent::getFieldByProperty($propertyName);
            }
        }
    }
}