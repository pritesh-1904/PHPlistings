<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Gateway;

class Response
{

    private $transactionReference = null;
    private $transactionId = null;
    private $transactionStatus = null;
    private $message = null;

    private $successful = false;
    private $pending = false;

    private $redirect = false;
    private $redirectUrl = null;
    private $redirectMethod = 'GET';
    private $redirectData = [];

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($value)
    {
        $this->message = $value;

        return $this;
    }

    public function getTransactionReference()
    {
        return $this->transactionReference;
    }

    public function setTransactionReference($value)
    {
        $this->transactionReference = $value;

        return $this;
    }

    public function getTransactionStatus()
    {
        return $this->transactionStatus;
    }

    public function setTransactionStatus($value)
    {
        $this->transactionStatus = $value;

        return $this;
    }

    public function getTransactionId()
    {
        return $this->transactionId;
    }

    public function setTransactionId($value)
    {
        $this->transactionId = $value;

        return $this;
    }

    public function isSuccessful()
    {
        return $this->successful;
    }

    public function setSuccessful()
    {
        $this->successful = true;

        return $this;
    }

    public function isPending()
    {
        return $this->pending;
    }

    public function setPending(bool $value)
    {
        $this->pending = $value;

        return $this;
    }

    public function isRedirect()
    {
        return $this->redirect;
    }

    public function setRedirect($value)
    {
        $this->redirect = $value;

        return $this;
    }

    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    public function setRedirectUrl($value)
    {
        $this->redirectUrl = $value;

        return $this;
    }

    public function getRedirectMethod()
    {
        return $this->redirectMethod;
    }

    public function setRedirectMethod($value)
    {
        $this->redirectMethod = $value;

        return $this;
    }

    public function getRedirectData()
    {
        return $this->redirectData;
    }

    public function setRedirectData(array $value)
    {
        $this->redirectData = $value;

        return $this;
    }

}
