<?php
require_once './PaynetProcess.php';

use PaynetEasy\Paynet\Workflow\Form         as FormQuery;
use PaynetEasy\Paynet\OrderData\Data;
use Exception;

class Form extends Sale
{
    public function init()
    {
        parent::init();

        // for a credit card form should not be determined
        $this->card         = new Data;
    }

    public function process_start()
    {
        // Step 1.
        // Create a query Sale
        $this->query            = new FormQuery($this->transport);

        // Configurating it
        $this->query->setConfig($this->config);

        // Assign Query data
        $this->order->setCustomer($this->customer);
        $this->query->setOrder($this->order);

        // Step 2. Process query
        try
        {
            $this->processResponse($this->query->process());
        }
        catch(Exception $e)
        {
            $this->out_error($e);
        }
    }

}