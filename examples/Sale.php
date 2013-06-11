<?PHP
require_once './PaynetProcess.php';

use \PaynetEasy\Paynet\Queries\SaleQuery         as PaynetSale;
use \PaynetEasy\Paynet\Queries\StatusQuery;
use \PaynetEasy\Paynet\Callbacks\Redirect3D;
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
            $this->processResponse($this->query->createRequest());
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
            $this->processResponse($this->query->createRequest());
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
            $this->processResponse($this->query->createRequest($_POST));
        }
        catch(Exception $e)
        {
            $this->out_error($e);
        }
    }
}