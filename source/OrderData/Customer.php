<?php
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
            case 'address':
            {
                return 'setAddress';
            }
            default:
            {
                return parent::getSetterByField($fieldName);
            }
        }
    }
}