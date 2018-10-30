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
        $data = $this->data;
        unset($data['url']);
        $more = http_build_query($data);
        if (strpos($this->data['url'], '?') !== false) {
            return $this->data['url'].'&'.$more;
        }
        return $this->data['url'].'?'.$more;
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