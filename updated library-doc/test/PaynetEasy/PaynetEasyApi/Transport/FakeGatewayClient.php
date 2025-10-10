<?php
namespace PaynetEasy\PaynetEasyApi\Transport;

class FakeGatewayClient implements GatewayClientInterface
{
    /**
     * @var Request
     */
    static public $request;

    /**
     * @var Response
     */
    static public $response;

    public function makeRequest(Request $request)
    {
        static::$request = $request;

        return static::$response;
    }
}
