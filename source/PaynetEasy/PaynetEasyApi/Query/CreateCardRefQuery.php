<?php
namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\Utils\Validator;
use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
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
        array('client_orderid',     'clientPaymentId',              true,   Validator::ID),
        array('orderid',            'paynetPaymentId',              true,   Validator::ID),
        // generated
        array('control',             null,                          true,    null),
        // from config
        array('login',               null,                          true,    null)
    );

    /**
     * {@inheritdoc}
     */
    static protected $controlCodeDefinition = array
    (
        'login',
        'clientPaymentId',
        'paynetPaymentId',
        'control'
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
     * Recurrent card class
     *
     * @var string
     */
    static protected $recurrentCardClass  = '\PaynetEasy\PaynetEasyApi\PaymentData\RecurrentCard';

    /**
     * {@inheritdoc}
     */
    protected function validatePayment(Payment $payment)
    {
        parent::validatePayment($payment);

        $this->checkPaymentProcessingStage($payment);
    }

    /**
     * {@inheritdoc}
     */
    protected function validateResponseOnSuccess(Payment $payment, Response $response)
    {
        parent::validateResponseOnSuccess($payment, $response);

        $this->checkPaymentProcessingStage($payment);
    }

    /**
     * {@inheritdoc}
     */
    protected function updatePaymentOnSuccess(Payment $payment, Response $response)
    {
        parent::updatePaymentOnSuccess($payment, $response);

        if($response->isApproved())
        {
            $recurrentCard = new static::$recurrentCardClass;
            $recurrentCard->setCardReferenceId($response->getCardReferenceId());

            $payment->setRecurrentCardFrom($recurrentCard);
        }
    }

    /**
     * Check Payment transport stage and bank status.
     * State must be STAGE_FINISHED and status must be STATUS_APPROVED.
     *
     * @param       Payment        $payment        Payment for checking
     */
    protected function checkPaymentProcessingStage(Payment $payment)
    {
        if (!$payment->isFinished() || !$payment->isApproved())
        {
            throw new ValidationException('Only approved and ended Payment can be used for create-card-ref-id');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function setConfig(array $config)
    {
        parent::setConfig($config);

        if (!empty($config['recurrent_card_class']))
        {
            static::$recurrentCardClass = $config['recurrent_card_class'];
        }
    }
}