<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class Email
    extends \App\Src\Orm\Model
{

    protected $table = 'emails';
    protected $searchable = [
        'recipient_id' => ['recipient_id', 'eq'],
    ];
    protected $sortable = [
        'id' => ['id'],
        'added_datetime' => ['added_datetime'],
    ];

    public function template()
    {
        return $this->belongsTo('App\Models\EmailTemplate');
    }

    public function recipient()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function setTemplate($template)
    {
        $this->template()->associate($template);

        if (false !== $template->isModeratable()) {
            $this->setStatus('pending');
        } else {
            $this->setStatus('queued');
        }

        return $this;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function setRecipient($recipient_id)
    {
        $this->recipient_id = $recipient_id;

        return $this;
    }

    public function getRecipient()
    {
        return $this->recipient_id;
    }

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    public function getError()
    {
        return $this->error;
    }

    public function setFrom($from)
    {
        if (null === $from) {
            return $this;
        }

        if (is_string($from)) {
            $this->from_email = $from;
        } else if (is_array($from) && count($from) == 1) {
            $this->from_email = key($from);
            $this->from_name = $from[key($from)];
        } else {
            throw new \Exception('Incorrect FROM value');
        }

        return $this;
    }

    public function getFrom()
    {
        if (null !== $from = $this->getTemplate()->getFrom($this->getData())) {
            return $from;
        }

        if (null !== $this->from_email && '' != $this->from_email) {
            return [$this->from_email => $this->from_name];
        }

        return [config()->email->from_email => config()->email->from_name];
    }

    public function setTo($to)
    {
        if (null === $to) {
            return $this;
        }

        if (is_string($to)) {
            $this->to_email = $to;
        } else if (is_array($to) && count($to) == 1) {
            $this->to_email = key($to);
            $this->to_name = $to[key($to)];
        } else {
            throw new \Exception('Incorrect TO value');
        }

        return $this;
    }

    public function getTo()
    {
        if (null !== $to = $this->getTemplate()->getTo($this->getData())) {
            return $to;
        }

        if (null !== $this->to_email && '' != $this->to_email) {
            return [$this->to_email => $this->to_name];
        }

        throw new \Exception('Missing TO address');
    }

    public function setReplyTo($reply_to)
    {
        if (null === $reply_to) {
            return $this;
        }

        $this->reply_to = $reply_to;

        return $this;
    }

    public function getReplyTo()
    {
        if (null !== $reply_to = $this->getTemplate()->getReplyTo($this->getData())) {
            return $reply_to;
        }

        if (null !== $this->get('reply_to')) {
            return $this->reply_to;
        }
    }

    public function setData(array $data = [])
    {
        $this->data = serialize($data);

        return $this;
    }

    public function getData()
    {
        if (false !== $return = unserialize($this->data)) {
            return $return;
        }

        return [];
    }

    public function getSubject()
    {
        return $this->getTemplate()->getSubject($this->getData());
    }

    public function getBody()
    {
        return $this->getTemplate()->getBody($this->getData());
    }

    public function getPriority()
    {
        return $this->getTemplate()->getPriority();
    }

    public function extractEmail($data)
    {
        if (is_array($data)) {
            if (is_numeric(key($data))) {
                return $data[0];
            } else {
                return key($data);
            }
        }

        return $data;
    }

    public function extractName($data)
    {
        if (is_array($data)) {
            if (false === is_numeric(key($data))) {
                return $data[key($data)];
            }
        }

        return '';
    }

}
