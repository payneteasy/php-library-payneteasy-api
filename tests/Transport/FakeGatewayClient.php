<?php
namespace PaynetEasy\Paynet\Transport;

class FakeGatewayClient implements GatewayClientInterface
{
    static public $response;

    static public $response2;

    static public $request;

    public function makeRequest(Request $request)
    {
        static::$request      = $request;

        $response           = static::$response;

        if(!empty(static::$response2))
        {
            static::$response = static::$response2;
        }

        return $response;
    }
}

?>