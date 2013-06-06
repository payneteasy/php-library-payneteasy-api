<?php
namespace PaynetEasy\Paynet\Transport;

use \PaynetEasy\Paynet\Responses\Response;

class Transport implements TransportI
{
    public $response;

    public $response2;

    public $request;

    public function query($request)
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