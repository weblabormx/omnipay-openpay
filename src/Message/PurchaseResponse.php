<?php

namespace Omnipay\OpenPay\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Class PurchaseResponse
 * @package Omnipay\OpenPay\Message
 */
class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    /**
     * Gateway endpoint
     * @var string
     */
    protected $endpoint;

    /**
     * Set successful to false, as transaction is not completed yet
     * @return bool
     */
    public function isSuccessful()
    {
        return false;
    }

    /**
     * Mark purchase as redirect type
     * @return bool
     */
    public function isRedirect()
    {
        return true;
    }

    /**
     * Get redirect URL
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->data['url'];
    }

    /**
     * Get redirect method
     * @return string
     */
    public function getRedirectMethod()
    {
        return 'GET';
    }

    /**
     * Get redirect data
     * @return array|mixed
     */
    public function getRedirectData()
    {
        return [];
    }
}