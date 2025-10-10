<?php
namespace PaynetEasy\PaynetEasyApi\Transport;

interface GatewayClientInterface
{
    /**
     * Make request to the PaynetEasy gateway
     *
     * @param   \PaynetEasy\PaynetEasyApi\Transport\Request    $request    Request data
     *
     * @return  \PaynetEasy\PaynetEasyApi\Transport\Response               Response data
     */
    public function makeRequest(Request $request);
}