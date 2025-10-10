<?php

namespace PaynetEasy\PaynetEasyApi\PaymentData;

class BillingAddress extends Data
{
    /**
     * Customer’s address line 1
     *
     * @var string
     */
    protected $firstLine;

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
     * Set customer’s address line 1
     *
     * @param       string      $firstLine      Customer’s address line 1
     *
     * @return      self
     */
    public function setFirstLine($firstLine)
    {
        $this->firstLine = $firstLine;

        return $this;
    }

    /**
     * Get customer’s address line 1
     *
     * @return      string
     */
    public function getFirstLine()
    {
        return $this->firstLine;
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
}