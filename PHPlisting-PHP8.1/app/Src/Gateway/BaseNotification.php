<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Gateway;

class BaseNotification
{

    private $transactionReference = null;
    private $transactionId = null;
    private $transactionStatus = null;
    private $message = null;

    private $successful = false;

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

}
