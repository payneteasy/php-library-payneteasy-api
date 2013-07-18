<?php

namespace PaynetEasy\PaynetEasyApi\PaymentData;

/**
 * Container for customer data
 *
 */
class Customer extends Data
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
     * Set customer’s first name
     *
     * @param       string      $firstName      Customer’s first name
     *
     * @return      self
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get customer’s first name
     *
     * @return      string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set customer’s last name
     *
     * @param       string      $lastName       Customer’s last name
     *
     * @return      self
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get customer’s last name
     *
     * @return      string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set customer’s address line 1
     *
     * @param       string      $address        Customer’s address line 1
     *
     * @return      self
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get customer’s address line 1
     *
     * @return      string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set customer’s country(two-letter country code)
     *
     * @param       string      $country        Customer’s country
     *
     * @return      self
     */
    public function setCountry($country)
    {
        $this->country = strtoupper($country);

        return $this;
    }

    /**
     * Get customer’s country(two-letter country code)
     *
     * @return      string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set customer’s state (two-letter US state code)
     *
     * @param       string      $state          Customer’s state
     *
     * @return      self
     */
    public function setState($state)
    {
        $this->state = strtoupper($state);

        return $this;
    }

    /**
     * Get customer’s state (two-letter US state code)
     *
     * @return      string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set customer’s city
     *
     * @param       string      $city           Customer’s city
     *
     * @return      self
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get customer’s city
     *
     * @return      string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set customer’s ZIP code
     *
     * @param       string      $zipCode        Customer’s ZIP code
     *
     * @return      self
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get customer’s ZIP code
     *
     * @return      string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Set customer’s full international phone number,
     * including country code.
     *
     * @param       string      $phone          Customer’s full international phone number
     *
     * @return      self
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get customer’s full international phone number,
     * including country code.
     *
     * @return      string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set customer’s full international cell phone number,
     * including country code.
     *
     * @param       string      $cellPhone      Customer’s full international cell phone number
     *
     * @return      self
     */
    public function setCellPhone($cellPhone)
    {
        $this->cellPhone = $cellPhone;

        return $this;
    }

    /**
     * Get customer’s full international cell phone number,
     * including country code.
     *
     * @return      string
     */
    public function getCellPhone()
    {
        return $this->cellPhone;
    }

    /**
     * Set customer’s email address
     *
     * @param       string      $email          Customer’s email address
     *
     * @return      self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get customer’s email address
     *
     * @return      string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set customer’s date of birth, in the format MMDDYY
     *
     * @param       integer     $birthday       Customer’s date of birth
     *
     * @return      self
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get customer’s date of birth, in the format MMDDYY
     *
     * @return      integer
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set last four digits of the customer’s social security number
     *
     * @param       integer     $ssn            Last four digits of the customer’s social security number
     *
     * @return      self
     */
    public function setSsn($ssn)
    {
        $this->ssn = $ssn;

        return $this;
    }

    /**
     * Get last four digits of the customer’s social security number
     *
     * @return      integer
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