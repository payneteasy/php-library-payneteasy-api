<?php
namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\Query\Prototype\Query;
use PaynetEasy\PaynetEasyApi\Util\Validator;
use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\Transport\Response;

/**
 * @see http://wiki.payneteasy.com/index.php/PnE:Sale_Transactions#Payment_status
 */
class StatusQuery extends Query
{
    /**
     * {@inheritdoc}
     */
    static protected $requestFieldsDefinition = array
    (
        // mandatory
        array('client_orderid',     'payment.clientId',                 true,    Validator::ID),
        array('orderid',            'payment.paynetId',                 true,    Validator::ID),
        array('login',              'queryConfig.login',                true,    Validator::MEDIUM_STRING)
    );

    /**
     * {@inheritdoc}
     */
    static protected $signatureDefinition = array
    (
        'queryConfig.login',
        'payment.clientId',
        'payment.paynetId',
        'queryConfig.signingKey'
    );

    /**
     * {@inheritdoc}
     */
    static protected $responseFieldsDefinition = array
    (
        'type',
        'status',
        'paynet-order-id',
        'merchant-order-id',
        'serial-number'
    );

    /**
     * {@inheritdoc}
     */
    static protected $successResponseType = 'status-response';

    /**
     * {@inheritdoc}
     */
    protected function updatePaymentTransactionOnSuccess(PaymentTransaction $paymentTransaction, Response $response)
    {
        parent::updatePaymentTransactionOnSuccess($paymentTransaction, $response);

        $this->setNeededAction($response);
        $this->setFieldsFromResponse($paymentTransaction, $response);
    }

    /**
     * Sets action, that library client have to execute.
     *
     * @param       Response        $response       PaynetEasy response.
     */
    protected function setNeededAction(Response $response) {
        if ($response->hasHtml())
        {
            $response->setNeededAction(Response::NEEDED_SHOW_HTML);
        }
        elseif ($response->isProcessing())
        {
            $response->setNeededAction(Response::NEEDED_STATUS_UPDATE);
        }
    }

    /**
     * Fill fields of payment data objects by date from PaynetEasy response.
     *
     * @param       PaymentTransaction      $paymentTransaction     Payment transaction to fill.
     * @param       Response                $response               PaynetEasy response to get data from.
     */
    protected function setFieldsFromResponse(PaymentTransaction $paymentTransaction, Response $response) {
        $payment = $paymentTransaction->getPayment();
        $card = $payment->getRecurrentCardFrom();

        if ($response->offsetExists('card-ref-id'))
        {
            $card->setPaynetId($response['card-ref-id']);
        }

        if ($response->offsetExists('last-four-digits'))
        {
            $card->setLastFourDigits($response['last-four-digits']);
        }

        if ($response->offsetExists('bin'))
        {
            $card->setBin($response['bin']);
        }

        if ($response->offsetExists('cardholder-name'))
        {
            $card->setCardPrintedName($response['cardholder-name']);
        }

        if ($response->offsetExists('card-exp-month'))
        {
            $card->setExpireMonth($response['card-exp-month']);
        }

        if ($response->offsetExists('card-exp-year'))
        {
            $card->setExpireYear($response['card-exp-year']);
        }

        if ($response->offsetExists('card-hash-id'))
        {
            $card->setCardHashId($response['card-hash-id']);
        }

        if ($response->offsetExists('card-type'))
        {
            $card->setCardType($response['card-type']);
        }
    }
}