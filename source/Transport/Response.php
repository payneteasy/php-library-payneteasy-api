<?PHP
namespace PaynetEasy\Paynet\Transport;

use ArrayObject;
use PaynetEasy\Paynet\Exception\PaynetException;
use RuntimeException;

class Response extends ArrayObject
{
    /**
     * Need to update order status
     */
    const NEEDED_STATUS_UPDATE  = 'status_update';

    /**
     * Need to show html from response
     */
    const NEEDED_SHOW_HTML      = 'show_html';

    /**
     * Need redirect to response url
     */
    const NEEDED_REDIRECT       = 'redirect';

    static protected $allowedNeededActions = array
    (
        self::NEEDED_STATUS_UPDATE,
        self::NEEDED_SHOW_HTML,
        self::NEEDED_REDIRECT
    );

    /**
     * Action needed after API method request ended
     *
     * @var string
     */
    protected $neededAction;

    public function __construct($response = array())
    {
        parent::__construct(array_map('trim', $response));
    }

    /**
     * Set action needed after API method request ended
     *
     * @param       string      $neededAction           Action needed after API method request ended
     *
     * @return      self
     */
    public function setNeededAction($neededAction)
    {
        if (!in_array($neededAction, static::$allowedNeededActions))
        {
            throw new RuntimeException("Unknown needed action: {$neededAction}");
        }

        $this->neededAction = $neededAction;

        return $this;
    }

    /**
     * Get action needed after API method request ended
     *
     * @return      string
     */
    public function getNeededAction()
    {
        return $this->neededAction;
    }

    /**
     * True if Response has html to display
     *
     * @return      boolean
     */
    public function hasHtml()
    {
        return strlen($this->html()) > 0;
    }

    /**
     * True if Response has url for redirect
     *
     * @return      boolean
     */
    public function hasRedirectUrl()
    {
        return strlen($this->redirectUrl()) > 0;
    }

    /**
     * True if status update needed
     *
     * @return      boolean
     */
    public function isStatusUpdateNeeded()
    {
        return $this->getNeededAction() == self::NEEDED_STATUS_UPDATE;
    }

    /**
     * True if html show needed
     *
     * @return      boolean
     */
    public function isShowHtmlNeeded()
    {
        return $this->getNeededAction() == self::NEEDED_SHOW_HTML;
    }

    /**
     * True if redirect needed
     *
     * @return      boolean
     */
    public function isRedirectNeeded()
    {
        return $this->getNeededAction() == self::NEEDED_REDIRECT;
    }

    public function html()
    {
        return $this->getValue('html');
    }

    public function type()
    {
        return strtolower($this->getValue('type'));
    }

    public function status()
    {
        return strtolower($this->getValue('status'));
    }

    public function serialNumber()
    {
        return $this->getValue('serial-number');
    }

    public function descriptor()
    {
        return $this->getValue('descriptor');
    }

    public function orderId()
    {
        return $this->getAnyKey(array('merchant-order-id', 'client_orderid', 'merchant_order'));
    }

    public function paynetOrderId()
    {
        return $this->getAnyKey(array('orderid', 'paynet-order-id'));
    }

    public function redirectUrl()
    {
        return $this->getValue('redirect-url');
    }

    public function control()
    {
        return $this->getAnyKey(array('control', 'merchant_control'));
    }

    public function errorMessage()
    {
        return $this->getAnyKey(array('error_message', 'error-message'));
    }

    public function errorCode()
    {
        return $this->getAnyKey(array('error_code', 'error-code'));
    }

    public function isError()
    {
                //  If type equals "validation-error" or "error", "error-message"
                //  and "error-code" parameters contain error details.
        return    in_array($this->type(), array('validation-error', 'error'))
               || $this->status() == 'error';
    }

    public function isApproved()
    {
        return $this->status() === 'approved';
    }

    public function isProcessing()
    {               // 1. If status defined and equal "processing"
        return      ($this->status() === 'processing')
                    // 2. Or status undefined but type not equal error
                ||  (   !strlen($this->status())
                     && !in_array($this->type(), array('validation-error', 'error')));
    }

    public function isDeclined()
    {
        // The state "declined" has a few cases:
        // 1. When the status === declined
        // 2. When the status === filtered
        // 3. And in other statuses
        //
        // Therefore, the state declined calculated indirectly
        return !$this->isApproved() && !$this->isProcessing() && !$this->isError();
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
        elseif(strlen($this->html()))
        {
            echo $this->html();
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

    protected function getAnyKey(array $keys)
    {
        foreach($keys as $key)
        {
            $value = $this->getValue($key);

            if(!is_null($value))
            {
                return $value;
            }
        }
    }

    protected function getValue($index)
    {
        if($this->offsetExists($index))
        {
            return $this->offsetGet($index);
        }
    }
}