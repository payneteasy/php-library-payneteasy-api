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
        return serialize(array($this->message, $this->code, $this->file, $this->line));
    }

    /**
     * Unserialize object from string
     *
     * @param       string      $serialized     Object data
     */
    public function unserialize($serialized)
    {
        list($this->message, $this->code, $this->file, $this->line) = unserialize($serialized);
    }
}