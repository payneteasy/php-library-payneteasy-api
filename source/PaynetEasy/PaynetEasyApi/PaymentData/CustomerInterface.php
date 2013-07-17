<?php

/**
 * @author Artem Ponomarenko <imenem@inbox.ru>
 */

namespace PaynetEasy\PaynetEasyApi\PaymentData;

interface CustomerInterface
{
    /**
     * Set customer’s first name
     *
     * @param       string      $firstName      Customer’s first name
     *
     * @return      self
     */
    public function setFirstName($firstName);

    /**
     * Get customer’s first name
     *
     * @return      string
     */
    public function getFirstName();

    /**
     * Set customer’s last name
     *
     * @param       string      $lastName       Customer’s last name
     *
     * @return      self
     */
    public function setLastName($lastName);

    /**
     * Get customer’s last name
     *
     * @return      string
     */
    public function getLastName();

    /**
     * Set customer’s address line 1
     *
     * @param       string      $address        Customer’s address line 1
     *
     * @return      self
     */
    public function setAddress($address);

    /**
     * Get customer’s address line 1
     *
     * @return      string
     */
    public function getAddress();

    /**
     * Set customer’s country(two-letter country code)
     *
     * @param       string      $country        Customer’s country
     *
     * @return      self
     */
    public function setCountry($country);

    /**
     * Get customer’s country(two-letter country code)
     *
     * @return      string
     */
    public function getCountry();

    /**
     * Set customer’s state (two-letter US state code)
     *
     * @param       string      $state          Customer’s state
     *
     * @return      self
     */
    public function setState($state);

    /**
     * Get customer’s state (two-letter US state code)
     *
     * @return      string
     */
    public function getState();

    /**
     * Set customer’s city
     *
     * @param       string      $city           Customer’s city
     *
     * @return      self
     */
    public function setCity($city);

    /**
     * Get customer’s city
     *
     * @return      string
     */
    public function getCity();

    /**
     * Set customer’s ZIP code
     *
     * @param       string      $zipCode        Customer’s ZIP code
     *
     * @return      self
     */
    public function setZipCode($zipCode);

    /**
     * Get customer’s ZIP code
     *
     * @return      string
     */
    public function getZipCode();

    /**
     * Set customer’s full international phone number,
     * including country code.
     *
     * @param       string      $phone          Customer’s full international phone number
     *
     * @return      self
     */
    public function setPhone($phone);

    /**
     * Get customer’s full international phone number,
     * including country code.
     *
     * @return      string
     */
    public function getPhone();

    /**
     * Set customer’s full international cell phone number,
     * including country code.
     *
     * @param       string      $cellPhone      Customer’s full international cell phone number
     *
     * @return      self
     */
    public function setCellPhone($cellPhone);

    /**
     * Get customer’s full international cell phone number,
     * including country code.
     *
     * @return      string
     */
    public function getCellPhone();

    /**
     * Set customer’s email address
     *
     * @param       string      $email          Customer’s email address
     *
     * @return      self
     */
    public function setEmail($email);

    /**
     * Get customer’s email address
     *
     * @return      string
     */
    public function getEmail();

    /**
     * Set customer’s date of birth, in the format MMDDYY
     *
     * @param       integer     $birthday       Customer’s date of birth
     *
     * @return      self
     */
    public function setBirthday($birthday);

    /**
     * Get customer’s date of birth, in the format MMDDYY
     *
     * @return      integer
     */
    public function getBirthday();

    /**
     * Set last four digits of the customer’s social security number
     *
     * @param       integer     $ssn            Last four digits of the customer’s social security number
     *
     * @return      self
     */
    public function setSsn($ssn);

    /**
     * Get last four digits of the customer’s social security number
     *
     * @return      integer
     */
    public function getSsn();
}