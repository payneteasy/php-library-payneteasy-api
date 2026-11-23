<?php

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig;
use PaynetEasy\PaynetEasyApi\Transport\Response;
use PaynetEasy\PaynetEasyApi\PaymentProcessor;

/**
 * Returns the configuration for the requests
 *
 * @return array
 */
$getQueryConfig = function()
{
    /**
     * NOTE: HTTP_HOST is client-controlled. In production build these URLs
     * from a configured base URL (e.g. 'https://shop.example.com') instead.
     */
    return new QueryConfig(array
    (
        /**
         * Merchant account endpoint, issued on connection
         *
         * @see http://wiki.payneteasy.com/index.php/PnE:Introduction#PaynetEasy_Objects
         * @see http://wiki.payneteasy.com/index.php/PnE:Introduction#Endpoint
         */
        'end_point'                 =>  253,
        /**
         * Merchant login, issued on connection
         *
         * @see http://wiki.payneteasy.com/index.php/PnE:Introduction#PaynetEasy_Users
         */
        'login'                     => 'rp-merchant1',
        /**
         * Merchant key for signing requests, issued on connection
         */
        'signing_key'               => '3FD4E71A-D84E-411D-A613-40A0FB9DED3A',
        /**
         * URL the customer is redirected to after the request
         *
         * @see http://wiki.payneteasy.com/index.php/PnE:Sale_Transactions#3D_redirect
         * @see http://wiki.payneteasy.com/index.php/PnE:Payment_Form_integration#Payment_Form_final_redirect
         */
        'redirect_url'              => "http://{$_SERVER['HTTP_HOST']}{$_SERVER['SCRIPT_NAME']}?stage=processCustomerReturn",
        /**
         * URL the customer is redirected to after the request
         *
         * @see http://wiki.payneteasy.com/index.php/PnE:Merchant_Callbacks
         */
        'callback_url'              => "http://{$_SERVER['HTTP_HOST']}{$_SERVER['SCRIPT_NAME']}?stage=processPaynetEasyCallback",
        /**
         * Library mode: sandbox, production
         *
         * @see \PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig::$gatewayMode
         * @see \PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig::$allowedGatewayModes
         * @see \PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig::setGatewayUrl()
         * @see \PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig::getGatewayUrl()
         */
        'gateway_mode'              => QueryConfig::GATEWAY_MODE_SANDBOX,
        /**
         * PaynetEasy gateway URL for the sandbox mode
         *
         * @see \PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig::getGatewayUrl()
         */
        'gateway_url_sandbox'       => 'https://sandbox.domain.com/paynet/api/v2/',
        /**
         * PaynetEasy gateway URL for the production mode
         *
         * @see \PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig::getGatewayUrl()
         */
        'gateway_url_production'    => 'https://payment.domain.com/paynet/api/v2/'
    ));
};

/**
 * Loads the payment from the session
 *
 * @return      Payment        Payment
 */
$loadPaymentTransaction = function()
{
    if (!empty($_SESSION['payment_transaction']))
    {
        return unserialize($_SESSION['payment_transaction']);
    }
};

/**
 * Saves the payment to the session
 *
 * @param       PaymentTransaction      $paymentTransaction     Payment transaction
 */
$savePaymentTransaction = function(PaymentTransaction $paymentTransaction)
{
    $_SESSION['payment_transaction'] = serialize($paymentTransaction);
};

/**
 * Shows a page with the text "payment is processing",
 * that refreshes itself after some time
 *
 * @see ./common/waitPage.html
 */
$displayWaitPage = function()
{
    // NOTE: HTTP_HOST is client-controlled, use a configured base URL in production
    $formAction = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['SCRIPT_NAME']}?stage=updateStatus";
    include(__DIR__ . '/waitPage.php');
    exit;
};

/**
 * Outputs the html from the Paynet response,
 * that redirects the customer to 3-D Secure authentication
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Sale_Transactions#3D_Sale_transaction_diagram
 *
 * @param       Response       $response       Paynet server response
 */
$displayResponseHtml = function(Response $response)
{
    // output the received form that redirects to 3-D Secure authentication
    print $response->getHtml();
    exit;
};

/**
 * Redirects the customer to the URL from the Paynet response.
 * that leads to the payment form on the Paynet side
 *
 * @see http://wiki.payneteasy.com/index.php/PnE:Payment_Form_integration#General_Payment_Form_Process_Flow
 */
$redirectToResponseUrl = function(Response $response)
{
    // Redirect the customer to the payment form
    header("Location: {$response->getRedirectUrl()}");
    exit;
};

/**
 * Shows the payment status after the processing is finished
 *
 * @param       PaymentTransaction      $paymentTransaction     Payment transaction
 */
$displayEndedPayment = function(PaymentTransaction $paymentTransaction)
{
    // the payment is finished, show its status
    print "<pre>";
    print "Payment processing finished.\n";
    print "Payment status: '" . htmlspecialchars((string) $paymentTransaction->getPayment()->getStatus(), ENT_QUOTES, 'UTF-8') . "'.\n";
    print "Payment transaction status: '" . htmlspecialchars((string) $paymentTransaction->getStatus(), ENT_QUOTES, 'UTF-8') . "'.\n";
    print "</pre>";

    session_destroy();
};

/**
 * Logs the caught exception and shows a general error message
 *
 * @param       Exception       $exception      Exception
 */
$displayException = function(Exception $exception)
{
    // exception caught: log the details, show the customer a general message
    error_log((string) $exception);

    print "<pre>";
    print "An error occurred while processing the payment. Please try again later.\n";
    print "</pre>";
};

/**
 * Creates the payment processing service
 * and sets handlers for the events of the payment processing
 *
 * @see ./common/functions.php
 * @see PaynetEasy\PaynetEasyApi\PaymentProcessor::executeQuery()
 * @see PaynetEasy\PaynetEasyApi\PaymentProcessor::processCustomerReturn()
 * @see PaynetEasy\PaynetEasyApi\PaymentProcessor::processPaynetEasyCallback()
 */
$getPaymentProcessor = function() use ($displayException,
                                       $savePaymentTransaction,
                                       $displayWaitPage,
                                       $redirectToResponseUrl,
                                       $displayResponseHtml,
                                       $displayEndedPayment)
{
    return new PaymentProcessor(array
    (
        PaymentProcessor::HANDLER_CATCH_EXCEPTION     => $displayException,
        PaymentProcessor::HANDLER_SAVE_CHANGES        => $savePaymentTransaction,
        PaymentProcessor::HANDLER_STATUS_UPDATE       => $displayWaitPage,
        PaymentProcessor::HANDLER_REDIRECT            => $redirectToResponseUrl,
        PaymentProcessor::HANDLER_SHOW_HTML           => $displayResponseHtml,
        PaymentProcessor::HANDLER_FINISH_PROCESSING   => $displayEndedPayment
    ));
};