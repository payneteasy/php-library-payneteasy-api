<?PHP
namespace PaynetEasy\Paynet\Transport;

use PaynetEasy\Paynet\Exceptions\RequestException;
use PaynetEasy\Paynet\Responses\Response;
use Exception;

class PaynetClient implements GatewayClientInterface
{
    /**
     * Full url to Paynet gateway
     *
     * @var string
     */
    protected $gateway_url;

    /**
     * Gateway client user agent
     *
     * @var string
     */
    protected $user_agent = 'PaynetEasy-Client/1.0';

    /**
     * Paynet gateway client
     *
     * @param       string      $server     Domain
     * @param       string      $base_url   Base Url
     */
    public function __construct($server, $base_url = '/paynet/api/v2/')
    {
        $this->gateway_url = "https://{$server}/{$base_url}";
    }

    /**
     * {@inheritdoc}
     */
    public function makeRequest(Request $request)
    {
        $request->validate();

        try
        {
            $curl   = curl_init();
            $url    = "{$this->gateway_url}/{$request->getApiMethod()}/{$request->getEndPoint()}";

            /**
             * @todo add SSL client certificates
             * @todo add method for change curl options
             */
            curl_setopt_array
            (
                $curl,
                array
                (
                    CURLOPT_URL            => $url,
                    CURLOPT_HEADER         => 0,
                    CURLOPT_USERAGENT      => $this->user_agent,
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0,
                    CURLOPT_POST           => 1,
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_POSTFIELDS     => http_build_query($request->getArrayCopy())
                )
            );

            $response       = curl_exec($curl);

            if(curl_errno($curl))
            {
                throw new RequestException(curl_error($curl), curl_errno($curl));
            }

            if(curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200)
            {
                throw new RequestException
                (
                    sprintf
                    (
                    	'response http error code "%s"',
                         curl_getinfo($curl, CURLINFO_HTTP_CODE)
                    )
                );
            }

            if(empty($response))
            {
                throw new RequestException('response is empty');
            }

            $result = array();

            parse_str($response, $result);

            if(empty($result))
            {
                throw new RequestException('Response parsing error: ' . $response);
            }
        }
        catch (Exception $e)
        {
        }
        // finally
        {
            // Close the resource
            curl_close($curl);
        }

        // If exception has occured rethrow its
        if(isset($e))
        {
            throw $e;
        }

        return new Response($result);
    }
}