<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class EmailTemplate
    extends \App\Src\Orm\Model
{

    protected $table = 'emailtemplates';

    protected $fillable = [
        'priority',
        'active',
        'moderatable',
        'name',
        'from_email',
        'from_name',
        'to_email',
        'to_name',
        'reply_to',
        'subject',
        'body',
    ];
    protected $sortable = [
        'id' => ['id'],
        'name' => ['name'],
    ];

    public function emails()
    {
        return $this->hasMany('App\Models\Email');
    }

    public function getFrom(array $data = [])
    {
        if (null !== $this->from_email && '' != $this->from_email) {
            return [$this->replace($this->from_email, $data) => $this->replace($this->from_name, $data)];
        }
    }

    public function getTo(array $data = [])
    {
        if (null !== $this->to_email && '' != $this->to_email) {
            return [$this->replace($this->to_email, $data) => $this->replace($this->to_name, $data)];
        }
    }

    public function getReplyTo(array $data = [])
    {
        if (null !== $this->reply_to && '' != $this->reply_to) {
            return $this->replace($this->reply_to, $data);
        }
    }

    public function getSubject(array $data = [])
    {
        if (false === array_key_exists('site_name', $data)) {
            $data['site_name'] = config()->general->site_name;
        }

        return $this->replace($this->subject, $data);
    }

    public function getBody(array $data = [])
    {
        if (false === array_key_exists('signature', $data)) {
            $data['signature'] = \nl2br(config()->email->signature);
        }
        
        if (false === array_key_exists('site_name', $data)) {
            $data['site_name'] = config()->general->site_name;
        }

        return $this->replace(d($this->body), $data);
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function isModeratable()
    {
        return $this->moderatable == 1;
    }

    private function replace($item, array $replace = [])
    {
        foreach ($replace as $key => $value) {
            $item = str_replace('{' . $key . '}', $value ?? '', $item);
        }

        return $item;
    }

}
