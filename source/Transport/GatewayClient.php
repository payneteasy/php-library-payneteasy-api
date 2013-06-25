<?php
namespace PaynetEasy\Paynet\Transport;

use PaynetEasy\Paynet\Transport\Response;

use PaynetEasy\Paynet\Exception\RequestException;
use PaynetEasy\Paynet\Exception\ResponseException;

class GatewayClient implements GatewayClientInterface
{
    /**
     * Full url to Paynet gateway
     *
     * @var string
     */
    protected $gatewayUrl;

    /**
     * Curl options
     *
     * @var array
     */
    protected $curlOptions = array
    (
        CURLOPT_HEADER         => 0,
        CURLOPT_USERAGENT      => 'PaynetEasy-Client/1.0',
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_POST           => 1,
        CURLOPT_RETURNTRANSFER => 1
    );

    /**
     * Paynet gateway client
     *
     * @param       string      $gatewayUrl         Full url to Paynet API gateway
     */
    public function __construct($gatewayUrl)
    {
        $this->gatewayUrl = rtrim($gatewayUrl, '/');
    }

    /**
     * Set curl options
     *
     * @param       array       $curlOptions        Curl options
     *
     * @return      self
     */
    public function setCurlOptions(array $curlOptions)
    {
        foreach ($curlOptions as $optionName => $optionValue)
        {
            $this->setCurlOption($optionName, $optionValue);
        }

        return $this;
    }

    /**
     * Set curl option
     *
     * @param       string      $optionName         Curl option name
     * @param       string      $optionValue        Curl option value
     *
     * @return      self
     */
    public function setCurlOption($optionName, $optionValue)
    {
        $this->curlOptions[$optionName] = $optionValue;

        return $this;
    }

    /**
     * Delete all curl options
     *
     * @return      self
     */
    public function removeCurlOptions()
    {
        $this->curlOptions = array();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function makeRequest(Request $request)
    {
        $this->validateRequest($request);

        $responseString = $this->sendRequest($request);

        return $this->parseReponse($responseString);
    }

    /**
     * Executes request
     *
     * @param       Request         $request        Request to execute
     *
     * @return      string                          Paynet response
     *
     * @throws      RequestException                Error while executing request
     */
    protected function sendRequest(Request $request)
    {
        $url  = "{$this->gatewayUrl}/{$request->getApiMethod()}/{$request->getEndPoint()}";
        $curl = curl_init($url);

        curl_setopt_array($curl, $this->curlOptions);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($request->getRequestFields()));

        $response = curl_exec($curl);

        $error_message  = '';
        $error_code     = 0;

        if(curl_errno($curl))
        {
            $error_message  = 'Error occured: ' . curl_error($curl);
            $error_code     = curl_errno($curl);
        }
        elseif(curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200)
        {
            $error_message  = 'Error occured. HTTP code: ' . curl_getinfo($curl, CURLINFO_HTTP_CODE);
        }

        curl_close($curl);

        if (!empty($error_message))
        {
            throw new RequestException($error_message, $error_code);
        }
        else
        {
            return $response;
        }
    }

    /**
     * Parse Paynet response from string to Response object
     *
     * @param       string      $response       Paynet response as string
     *
     * @return      Response                    Paynet response as object
     *
     * @throws      ResponseException           Error while parsing
     */
    protected function parseReponse($response)
    {
        if(empty($response))
        {
            throw new ResponseException('Paynet response is empty');
        }

        $responseFields = array();

        parse_str($response, $responseFields);

        if(empty($responseFields))
        {
            throw new ResponseException('Can not parse response: ' . $response);
        }

        return new Response($responseFields);
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

        if (count($request->getRequestFields()) === 0)
        {
            throw new ValidationException('Request data is empty');
        }
    }
}