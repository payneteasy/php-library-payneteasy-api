<?php
namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\Utils\Validator;
use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\Transport\Response;
use PaynetEasy\PaynetEasyApi\Exception\ValidationException;

/**
 * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Card_Registration
 */
class CreateCardRefQuery extends AbstractQuery
{
    /**
     * {@inheritdoc}
     */
    static protected $requestFieldsDefinition = array
    (
        // mandatory
        array('client_orderid',     'payment.clientPaymentId',      true,   Validator::ID),
        array('orderid',            'payment.paynetPaymentId',      true,   Validator::ID),
        array('login',              'queryConfig.login',            true,   Validator::MEDIUM_STRING)
    );

    /**
     * {@inheritdoc}
     */
    static protected $signatureDefinition = array
    (
        'queryConfig.login',
        'payment.clientPaymentId',
        'payment.paynetPaymentId',
        'queryConfig.signingKey'
    );

    /**
     * {@inheritdoc}
     */
    static protected $responseFieldsDefinition = array
    (
        'type',
        'status',
        'card-ref-id',
        'serial-number'
    );

    /**
     * {@inheritdoc}
     */
    static protected $successResponseType = 'create-card-ref-response';

    /**
     * {@inheritdoc}
     */
    protected function validatePaymentTransaction(PaymentTransaction $paymentTransaction)
    {
        parent::validatePaymentTransaction($paymentTransaction);

        $this->checkPaymentTransactionStatus($paymentTransaction);
    }

    /**
     * {@inheritdoc}
     */
    protected function validateResponseOnSuccess(PaymentTransaction $paymentTransaction, Response $response)
    {
        parent::validateResponseOnSuccess($paymentTransaction, $response);

        $this->checkPaymentTransactionStatus($paymentTransaction);
    }

    /**
     * {@inheritdoc}
     */
    protected function updatePaymentTransactionOnSuccess(PaymentTransaction $paymentTransaction, Response $response)
    {
        parent::updatePaymentTransactionOnSuccess($paymentTransaction, $response);

        if($response->isApproved())
        {
            $paymentTransaction
                ->getPayment()
                ->getRecurrentCardFrom()
                ->setCardReferenceId($response->getCardReferenceId())
            ;
        }
    }

    /**
     * Check payment transaction processing stage and bank status.
     * State must be STAGE_FINISHED and status must be STATUS_APPROVED.
     *
     * @param       PaymentTransaction      $paymentTransaction     Payment for checking
     */
    protected function checkPaymentTransactionStatus(PaymentTransaction $paymentTransaction)
    {
        if (!$paymentTransaction->isFinished() || !$paymentTransaction->isApproved())
        {
            throw new ValidationException('Only approved and finished payment transaction can be used for create-card-ref-id');
        }
    }
}