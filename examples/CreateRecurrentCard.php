<?PHP
require_once './Sale.php';

use PaynetEasy\Paynet\OrderData\OrderInterface;

use PaynetEasy\Paynet\OrderData\RecurrentCard;
use PaynetEasy\Paynet\Transport\Response;
use PaynetEasy\Paynet\Query\CreateCardRefQuery;

use Exception;

class   CreateRecurrentCard extends Sale
{
    protected function processResponse(Response $response)
    {
        if(   $this->query->getOrder()->getState() === OrderInterface::STATE_END
           && $response->isApproved())
        {
            // Create Reccurent Card
            $this->createCardRef();
        }

        parent::process_response($response);
    }

    protected function createCardRef()
    {
        $query = new CreateCardRefQuery();

        $query->setConfig($this->config);
        $query->setOrder($this->order);

        $e                  = null;
        try
        {
            $request = $query->processOrder();
            $response = $this->transport->makeRequest($request);
            $query->processResponse($response);
        }
        catch(Exception $e)
        {
        }

        if($response->isApproved())
        {
            $this->order->setRecurrentCard(new RecurrentCard($response['cardrefid']));
        }

        return $response;
    }
}