<?PHP
require_once './PaynetProcess.php';

use \PaynetEasy\Paynet\Query\SaleQuery         as PaynetSale;
use \PaynetEasy\Paynet\Query\StatusQuery;
use \PaynetEasy\Paynet\Callback\Redirect3D;
use \Exception;

class Sale extends PaynetProcess
{
    public function process_start()
    {
        // Step 1.
        // Create a query Sale
        $this->query            = new PaynetSale($this->transport);

        // Configurating it
        $this->query->setConfig($this->config);

        // Assign Query data
        $this->order->setCreditCard($this->card);
        $this->order->setCustomer($this->customer);

        $this->query->setOrder($this->order);

        // Step 2. Process query
        try
        {
            $this->processResponse($this->query->processOrder());
        }
        catch(Exception $e)
        {
            $this->out_error($e);
        }
    }

    public function process_update()
    {
        // Step 1. Restoring Context

        // Restoring paynet order ID
        $this->order->setPaynetOrderId($_POST['orderid']);
        // And restoring client_orderid
        $this->order['client_orderid'] = $_POST['client_orderid'];

        // Create Status Query
        $this->query            = new StatusQuery($this->transport);

        $this->query->setOrder($this->order);
        $this->query->setConfig($this->config);

        // Step 2. Executing query
        try
        {
            $this->processResponse($this->query->processOrder());
        }
        catch(Exception $e)
        {
            $this->out_error($e);
        }
    }

    public function process_redirect()
    {
        // Step 1. Restoring Context

        // Restoring paynet order ID
        $this->order->setPaynetOrderId($_POST['orderid']);
        // And restoring client_orderid
        $this->order['client_orderid'] = $_POST['client_orderid'];

        // Create Status Query
        $this->query            = new Redirect3D($this->transport);

        $this->query->setOrder($this->order);
        $this->query->setConfig($this->config);

        // Step 2. Executing query
        try
        {
            $this->processResponse($this->query->processOrder($_POST));
        }
        catch(Exception $e)
        {
            $this->out_error($e);
        }
    }
}