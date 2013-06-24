<?php
namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\Utils\Validator;
use PaynetEasy\Paynet\OrderData\OrderInterface;
use PaynetEasy\Paynet\Transport\Response;
use PaynetEasy\Paynet\Exception\ValidationException;

/**
 * The implementation of the query STATUS
 * http://wiki.payneteasy.com/index.php?title=PnE%3ARecurrent_Transactions&setlang=en#Recurrent_Payments
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
    protected function validateOrder(OrderInterface $order)
    {
        parent::validateOrder($order);

        $this->checkOrderState($order);
    }

    /**
     * {@inheritdoc}
     */
    protected function validateResponse(OrderInterface $order, Response $response)
    {
        if(!isset($response['card-ref-id']))
        {
            throw new ValidationException('Field card-ref-id must be defined in response');
        }

        $this->checkOrderState($order);
    }

    /**
     * {@inheritdoc}
     */
    protected function updateOrder(OrderInterface $order, Response $response)
    {
        parent::updateOrder($order, $response);

        if($response->isApproved())
        {
            $order->createRecurrentCardFrom($response['card-ref-id']);
        }
    }

    /**
     * Check Order state and status.
     * State must be STATE_END and status must be STATUS_APPROVED.
     *
     * @param       OrderInterface      $order      Order for checking
     */
    protected function checkOrderState(OrderInterface $order)
    {
        if (    $order->getState()  !== OrderInterface::STATE_END
            ||  $order->getStatus() !== OrderInterface::STATUS_APPROVED)
        {
            throw new ValidationException('Only approved Order can be used for create-card-ref-id');
        }
    }
}