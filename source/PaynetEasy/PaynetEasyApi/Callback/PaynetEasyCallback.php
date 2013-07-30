<?php
namespace PaynetEasy\PaynetEasyApi\Callback;

use PaynetEasy\PaynetEasyApi\PaymentData\PaymentTransaction;
use PaynetEasy\PaynetEasyApi\Transport\CallbackResponse;

class PaynetEasyCallback extends AbstractCallback
{
    /**
     * {@inheritdoc}
     */
    public function processCallback(PaymentTransaction $paymentTransaction, CallbackResponse $callbackResponse)
    {
        $paymentTransaction->setProcessorType(PaymentTransaction::PROCESSOR_CALLBACK);
        $paymentTransaction->setProcessorName($callbackResponse->getType());

        parent::processCallback($paymentTransaction, $callbackResponse);
    }
}
