<?php
namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\Utils\Validator;
use PaynetEasy\Paynet\OrderData\OrderInterface;
use PaynetEasy\Paynet\Transport\Response;
use PaynetEasy\Paynet\Exception\ValidationException;

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
        array('client_orderid',     'clientOrderId',                true,   Validator::ID),
        array('orderid',            'paynetOrderId',                true,   Validator::ID),
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
        'clientOrderId',
        'paynetOrderId',
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
    static protected $recurrentCardClass  = '\PaynetEasy\Paynet\OrderData\RecurrentCard';

    /**
     * {@inheritdoc}
     */
    protected function validateOrder(OrderInterface $order)
    {
        parent::validateOrder($order);

        $this->checkOrderTransportStage($order);
    }

    /**
     * {@inheritdoc}
     */
    protected function validateResponseOnSuccess(OrderInterface $order, Response $response)
    {
        parent::validateResponseOnSuccess($order, $response);

        $this->checkOrderTransportStage($order);
    }

    /**
     * {@inheritdoc}
     */
    protected function updateOrderOnSuccess(OrderInterface $order, Response $response)
    {
        parent::updateOrderOnSuccess($order, $response);

        if($response->isApproved())
        {
            $recurrentCard = new static::$recurrentCardClass;
            $recurrentCard->setCardReferenceId($response->getCardReferenceId());

            $order->setRecurrentCardFrom($recurrentCard);
        }
    }

    /**
     * Check Order transport stage and bank status.
     * State must be STAGE_ENDED and status must be STATUS_APPROVED.
     *
     * @param       OrderInterface      $order      Order for checking
     */
    protected function checkOrderTransportStage(OrderInterface $order)
    {
        if (    $order->getTransportStage() !== OrderInterface::STAGE_ENDED
            ||  $order->getStatus()         !== OrderInterface::STATUS_APPROVED)
        {
            throw new ValidationException('Only approved and ended Order can be used for create-card-ref-id');
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