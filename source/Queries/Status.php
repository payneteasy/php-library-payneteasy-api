<?PHP
namespace PaynetEasy\Paynet\Queries;

use \PaynetEasy\Paynet\Exceptions\ConfigException;

/**
 * The implementation of the query STATUS
 * http://wiki.payneteasy.com/index.php/PnE:Sale_Transactions#Order_status
 */
class Status extends Query
{
    public function validate()
    {
        $this->validateConfig();

        if(empty($this->config['login']))
        {
            throw new ConfigException('login undefined');
        }

        if(!$this->getOrder())
        {
            throw new ConfigException('Order is not defined');
        }

        $this->getOrder()->validateShort();
    }

    public function process($data = null)
    {
        $this->validate();

        $query              = array_merge
        (
            $this->getOrder()->getContextData(),
            // Выделить этот код в отдельный класс
            array
            (
                'login'         => $this->config['login'],
                'control'       => $this->createControlCode(),
                '.method'       => $this->method,
                '.end_point'    => $this->config['end_point']
            )
        );

        return $this->sendQuery($query);
    }

    protected function createControlCode()
    {
        // This is SHA-1 checksum of the concatenation
        // login + client-order-id + paynet-order-id + merchant-control.
        return sha1
        (
            $this->config['login'].
            $this->getOrder()->getOrderCode().
            $this->getOrder()->getPaynetOrderId().
            $this->config['control']
        );
    }
}