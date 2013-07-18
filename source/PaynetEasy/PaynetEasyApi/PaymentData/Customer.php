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
     * Customer’s email address
     *
     * @var string
     */
    protected $email;

    /**
     * Customer’s IP address
     *
     * @var string
     */
    protected $ipAddress;

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
     * Set customer’s IP address
     *
     * @param       string      $ipAddress          Customer’s IP address
     *
     * @return      self
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * Get customer’s IP address
     *
     * @return      string
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
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
}