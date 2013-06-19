<?PHP
namespace PaynetEasy\Paynet\Transport;

use PaynetEasy\Paynet\Exception\RequestException;
use PaynetEasy\Paynet\Transport\Response;
use Exception;

class GatewayClient implements GatewayClientInterface
{
    /**
     * Full url to Paynet gateway
     *
     * @var string
     */
    protected $gatewayUrl;

    /**
     * Gateway client user agent
     *
     * @var string
     */
    protected $userAgent = 'PaynetEasy-Client/1.0';

    /**
     * Paynet gateway client
     *
     * @param       string      $gatewayUrl     Full url to Paynet API gateway
     */
    public function __construct($gatewayUrl)
    {
        $this->gatewayUrl = rtrim($gatewayUrl, '/');
    }

    /**
     * {@inheritdoc}
     */
    public function makeRequest(Request $request)
    {
        $this->validateRequest($request);

        try
        {
            $curl   = curl_init();
            $url    = "{$this->gatewayUrl}/{$request->getApiMethod()}/{$request->getEndPoint()}";

            /**
             * @todo add method for change curl options
             */
            curl_setopt_array
            (
                $curl,
                array
                (
                    CURLOPT_URL            => $url,
                    CURLOPT_HEADER         => 0,
                    CURLOPT_USERAGENT      => $this->userAgent,
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

    /**
     * Validates Request
     *
     * @param       Request                 $request        Request for validation
     *
     * @throws      ValidationException                     Request data is invalid
     */
    protected function validateRequest(Request $request)
    {
        if (strlen($request->getApiMethod()) == 0)
        {
            throw new ValidationException('Request api method is empty');
        }

        if (strlen($request->getEndPoint()) == 0)
        {
            throw new ValidationException('Request endpoint is empty');
        }

        if ($request->count() === 0)
        {
            throw new ValidationException('Request data is empty');
        }
    }
}