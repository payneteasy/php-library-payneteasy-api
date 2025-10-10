<?php
namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\Query\Prototype\Query;
use PaynetEasy\PaynetEasyApi\Util\Validator;
use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\Transport\Response;
use PaynetEasy\PaynetEasyApi\Exception\ValidationException;

/**
 * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Card_Registration
 */
class CreateCardRefQuery extends Query
{
    /**
     * {@inheritdoc}
     */
    static protected $requestFieldsDefinition = array
    (
        // mandatory
        array('client_orderid',     'payment.clientId',             true,   Validator::ID),
        array('orderid',            'payment.paynetId',             true,   Validator::ID),
        array('login',              'queryConfig.login',            true,   Validator::MEDIUM_STRING)
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
        $this->checkPaymentTransactionStatus($paymentTransaction);
        parent::validatePaymentTransaction($paymentTransaction);
    }

    /**
     * {@inheritdoc}
     */
    protected function validateResponseOnSuccess(PaymentTransaction $paymentTransaction, Response $response)
    {
        $this->checkPaymentTransactionStatus($paymentTransaction);
        parent::validateResponseOnSuccess($paymentTransaction, $response);
    }

    /**
     * {@inheritdoc}
     */
    protected function updatePaymentTransactionOnSuccess(PaymentTransaction $paymentTransaction, Response $response)
    {
        $paymentTransaction
            ->getPayment()
            ->getRecurrentCardFrom()
            ->setPaynetId($response->getCardPaynetId())
        ;
    }

    /**
     * Check, if payment transaction is finished and payment is not new.
     *
     * @param       PaymentTransaction      $paymentTransaction     Payment transaction for checking
     */
    protected function checkPaymentTransactionStatus(PaymentTransaction $paymentTransaction)
    {
        if (!$paymentTransaction->isFinished())
        {
            throw new ValidationException('Only finished payment transaction can be used for create-card-ref-id');
        }

        if (!$paymentTransaction->getPayment()->isPaid())
        {
            throw new ValidationException("Can not use new payment for create-card-ref-id. Execute 'sale' or 'preauth' query first");
        }
    }
}