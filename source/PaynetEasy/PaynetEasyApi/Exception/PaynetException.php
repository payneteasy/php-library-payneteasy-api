<?php
namespace PaynetEasy\PaynetEasyApi\Exception;

use Serializable;
use Exception;

class PaynetException extends Exception implements Serializable
{
    /**
     * Serialize all object scalar properties to string
     *
     * @return      string
     */
    public function serialize()
    {
        return serialize($this->__serialize());
    }

    /**
     * Object data for serialization (PHP 7.4+)
     *
     * @return      array
     */
    public function __serialize()
    {
        return array($this->message, $this->code, $this->file, $this->line);
    }

    /**
     * Unserialize object from string
     *
     * @param       string      $serialized     Object data
     */
    public function unserialize($serialized)
    {
        $this->__unserialize((array) unserialize($serialized));
    }

    /**
     * Restore object from serialized data (PHP 7.4+)
     *
     * @param       array       $data       Object data
     */
    public function __unserialize(array $data)
    {
        list($this->message, $this->code, $this->file, $this->line) = $data;
    }
}