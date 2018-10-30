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
        $url = $this->processPayment();
        return [
            'url' => $url
        ];
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
        $openpay = \Openpay::getInstance($this->getApiKey(), $this->getSecretKey());
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
            'order_id'    => $this->getTransactionId().'-'.rand(0,999999),
            'customer'    => $customer
        );

        if($type=='card') {
            $chargeRequest['confirm']      = false;
            $chargeRequest['redirect_url'] = $this->getReturnUrl();
        }

        $charge = $openpay->charges->create($chargeRequest);
        if($type=='card') {
            return $charge->payment_method->url;
        }

        if(!$this->is_production) { 
            $link = 'https://sandbox-dashboard.openpay.mx'; 
        } else { 
            $link = 'https://dashboard.openpay.mx';
        }

        if($type=='bank_account') {
            return "{$link}/spei-pdf/{$this->key}/{$charge->id}";
        }
        if($type=='store') {
            return "{$link}/paynet-pdf/{$this->key}/{$charge->payment_method->reference}";
        }

        throw new \Exception("Method Incorrect, you should choose between card, bank_account and store", 1);
    }
}