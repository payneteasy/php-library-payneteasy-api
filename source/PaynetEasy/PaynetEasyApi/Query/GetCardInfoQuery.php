<?php
namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\Utils\Validator;
use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\Transport\Response;
use PaynetEasy\PaynetEasyApi\Exception\ValidationException;

/**
 * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Get_Cardholder_details_with_Card_Reference_Identifier
 */
class GetCardInfoQuery extends AbstractQuery
{
    /**
     * {@inheritdoc}
     */
    static protected $requestFieldsDefinition = array
    (
        // mandatory
        array('cardrefid',          'recurrentCardFrom.cardReferenceId',    true,    Validator::ID),
        array('login',              'queryConfig.login',                    true,    Validator::MEDIUM_STRING)
    );

    /**
     * {@inheritdoc}
     */
    static protected $signatureDefinition = array
    (
        'queryConfig.login',
        'recurrentCardFrom.cardReferenceId',
        'queryConfig.signingKey'
    );

    /**
     * {@inheritdoc}
     */
    static protected $responseFieldsDefinition = array
    (
        'type',
        'card-printed-name',
        'expire-year',
        'expire-month',
        'bin',
        'last-four-digits',
        'serial-number'
    );

    /**
     * {@inheritdoc}
     */
    static protected $successResponseType = 'get-card-info-response';

    /**
     * {@inheritdoc}
     */
    protected function updatePaymentOnSuccess(Payment $payment, Response $response)
    {
        parent::updatePaymentOnSuccess($payment, $response);

        $payment->getRecurrentCardFrom()
            ->setCardPrintedName($response['card-printed-name'])
            ->setExpireYear($response['expire-year'])
            ->setExpireMonth($response['expire-month'])
            ->setBin($response['bin'])
            ->setLastFourDigits($response['last-four-digits']);
    }

    protected function validateResponseOnSuccess(Payment $payment, Response $response)
    {
        parent::validateResponseOnSuccess($payment, $response);

        if(!$payment->getRecurrentCardFrom())
        {
            throw new ValidationException('Recurrent card must be defined in Payment');
        }
    }
}