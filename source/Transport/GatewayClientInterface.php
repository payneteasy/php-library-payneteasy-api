<?PHP
namespace PaynetEasy\Paynet\Transport;

interface GatewayClientInterface
{
    /**
     * Make request to the Paynet gateway
     *
     * @param   \PaynetEasy\Paynet\Transport\Request    $request    Request data
     *
     * @return  \PaynetEasy\Paynet\Responses\Response               Response data
     */
    public function makeRequest(Request $request);
}