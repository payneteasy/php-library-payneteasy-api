<?PHP
namespace PaynetEasy\Paynet\Transport;

interface GatewayClientInterface
{
    /**
     * Make request to the Paynet gateway
     *
     * @param   \PaynetEasy\Paynet\Transport\Request    $request    Request data
     *
     * @return  \PaynetEasy\Paynet\Transport\Response               Response data
     */
    public function makeRequest(Request $request);
}