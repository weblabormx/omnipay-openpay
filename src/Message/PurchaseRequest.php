<?php

namespace Omnipay\OpenPay\Message;

use Omnipay\Common\Message\AbstractRequest;

/**
 * Class PurchaseRequest
 * @package Omnipay\OpenPay\Message
 */
class PurchaseRequest extends AbstractRequest
{
    /**
     * Sets the request account ID.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setApiKey($value)
    {
        return $this->setParameter('apiKey', $value);
    }

    /**
     * Get the request account ID.
     * @return $this
     */
    public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }

    /**
     * Sets the request secret key.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setSecretKey($value)
    {
        return $this->setParameter('secretKey', $value);
    }

    /**
     * Get the request secret key.
     * @return $this
     */
    public function getSecretKey()
    {
        return $this->getParameter('secretKey');
    }

    public function setName($value)
    {
        return $this->setParameter('name', $value);
    }

    public function getName()
    {
        return $this->getParameter('name');
    }

    public function setEmail($value)
    {
        return $this->setParameter('email', $value);
    }

    public function getEmail()
    {
        return $this->getParameter('email');
    }

    /**
     * Prepare data to send
     * @return array|mixed
     */
    public function getData()
    {
        $this->validate('amount', 'returnUrl', 'apiKey', 'secretKey', 'testMode', 'description', 'paymentMethod', 'name', 'email');
        return $this->processPayment();
    }

    /**
     * Send data and return response instance
     *
     * @param mixed $data
     *
     * @return \Omnipay\Common\Message\ResponseInterface|\Omnipay\OpenPay\Message\PurchaseResponse
     */
    public function sendData($data)
    {
        return $this->response = new PurchaseResponse($this, $data);
    }

    public function processPayment() 
    {
        $key = $this->getApiKey();
        $openpay = \Openpay::getInstance($key, $this->getSecretKey());
        \Openpay::setProductionMode(!$this->getTestMode());
        
        $customer = array(
            'name'  => $this->getParameter('name'),
            'email' => $this->getParameter('email')
        );
        
        $type = $this->getPaymentMethod();

        // store, card, bank_account
        $chargeRequest = array(
            "method"      => $type,
            'amount'      => (double) $this->getAmount(),
            'description' => $this->getDescription(),
            'send_email'  => false,
            'order_id'    => $this->getTransactionId(),
            'customer'    => $customer
        );

        if($type=='card') {
            $chargeRequest['confirm']      = false;
            $chargeRequest['redirect_url'] = $this->getReturnUrl();
        }

        $charge = $openpay->charges->create($chargeRequest);
        if($type=='card') {
            return ['url' => $charge->payment_method->url];
        }

        if($this->getTestMode()) { 
            $link = 'https://sandbox-dashboard.openpay.mx'; 
        } else { 
            $link = 'https://dashboard.openpay.mx';
        }

        if($type=='bank_account') {
            $link = "{$link}/spei-pdf/{$key}/{$charge->id}";
        }
        if($type=='store') {
            $link = "{$link}/paynet-pdf/{$key}/{$charge->payment_method->reference}";
        }

        return [
            'url' => $this->getReturnUrl(),
            'file_url' => $link
        ];
    }
}