<?php

namespace Omnipay\OpenPay\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Class CompletePurchaseResponse
 * @package Omnipay\OpenPay\Message
 */
class CompletePurchaseResponse extends AbstractResponse
{
    /**
     * Indicates whether transaction was successful
     * @return bool
     */
    public function isSuccessful()
    {
        return !empty($this->data['success']);
    }

    /**
     * Get transaction ID, generated by merchant
     * @return mixed|string
     */
    public function getTransactionId()
    {
        return array_get($this->data, 'EDP_BILL_NO');
    }

    /**
     * Get transaction reference, generated by gateway
     * @return mixed|null|string
     */
    public function getTransactionReference()
    {
        return array_get($this->data, 'EDP_TRANS_ID');
    }
}