<?php
namespace PaynetEasy\Paynet\Transport;

use \PaynetEasy\Paynet\Transport\Response;

class Transport implements GatewayClientInterface
{
    public $response;

    public $response2;

    public $request;

    public function makeRequest(Request $request)
    {
        $this->request      = $request;

        $response           = $this->response;

        if(!empty($this->response2))
        {
            $this->response = $this->response2;
        }

        return new Response($response);
    }
}

?>