<?PHP
namespace PaynetEasy\Paynet\Responses;

use \ArrayObject;
use \PaynetEasy\Paynet\Exceptions\PaynetException;

class   Response  extends ArrayObject
{
    public function __construct($array = array())
    {
        foreach($array as $key => $value)
        {
            $array[$key]        = trim($value);
        }

        parent::__construct($array);
    }

    protected function get_value($index)
    {
        if($this->offsetExists($index))
        {
            return $this->offsetGet($index);
        }
        else
        {
            return null;
        }
    }

    public function type()
    {
        return $this->get_value('type');
    }

    public function status()
    {
        return $this->get_value('status');
    }

    public function serialNumber()
    {
        return $this->get_value('serial-number');
    }

    public function descriptor()
    {
        return $this->get_value('descriptor');
    }

    public function orderId()
    {
        foreach(array('merchant-order-id', 'client_orderid', 'merchant_order') as $index)
        {
            $result     = $this->get_value($index);
            if(!is_null($result))
            {
                return $result;
            }
        }
    }

    public function paynetOrderId()
    {
        foreach(array('orderid', 'paynet-order-id') as $index)
        {
            $result     = $this->get_value($index);
            if(!is_null($result))
            {
                return $result;
            }
        }
    }

    public function redirectUrl()
    {
        return $this->get_value('redirect-url');
    }

    public function control()
    {
        foreach(array('control', 'merchant_control') as $index)
        {
            $result     = $this->get_value($index);
            if(!is_null($result))
            {
                return $result;
            }
        }
    }

    public function errorMessage()
    {
        foreach(array('error_message', 'error-message') as $index)
        {
            $result     = $this->get_value($index);
            if(!is_null($result))
            {
                return $result;
            }
        }

        return '';
    }

    public function errorCode()
    {
        foreach(array('error_code', 'error-code') as $index)
        {
            $result     = $this->get_value($index);
            if(!is_null($result))
            {
                return (int)$result;
            }
        }

        return 0;
    }

    public function isError()
    {
        //  If type equals "validation-error" or "error", "error-message"
        //  and "error-code" parameters contain error details.
        if($this->offsetExists('type')
        && in_array($this['type'], array('validation-error', 'error')))
        {
            return true;
        }
        elseif($this->offsetExists('status')
        && in_array($this['status'], array('error'))
        )
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function isApproved()
    {
        if($this->status() === 'approved')
        {
            return true;
        }

        return false;
    }

    public function isProcessing()
    {
        // 1. If status defined and equal "processing"
        if($this->status() === 'processing')
        {
            return true;
        }
        // 2. Or status undefined but type not equal error
        elseif(
           !$this->offsetExists('status')
        && $this->offsetExists('type')
        && !in_array($this['type'], array('validation-error', 'error'))
        )
        {
            return true;
        }

        return false;
    }

    public function isDeclined()
    {
        // The state "declined" has a few cases:
        // 1. When the status === declined
        // 2. When the status === filtered
        // 3. And in other statuses
        //
        // Therefore, the state declined calculated indirectly
        //
        if(!$this->isApproved() && !$this->isProcessing() && !$this->isError())
        {
            return true;
        }

        return false;
    }

    /**
     * This method or out HTML for redirect
     * or send header Location
     */
    public function redirect()
    {
        if($this->redirectUrl())
        {
            header('Location: '.$this->redirectUrl());
        }
        elseif($this->offsetExists('html'))
        {
            echo $this->offsetGet('html');
        }
        else
        {
            throw new PaynetException('Redirect inpossible!');
        }
    }

    public function error()
    {
        //  If type equals "validation-error" or "error", "error-message"
        //  and "error-code" parameters contain error details.
        if($this->isError())
        {
            return new PaynetException($this->errorMessage(), $this->errorCode());
        }
        else
        {
            return false;
        }
    }
}