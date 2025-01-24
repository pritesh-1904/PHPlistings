<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2022 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class Comment
    extends \App\Src\Orm\Model
{

    protected $table = 'comments';
    protected $fillable = [
        'title',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function review()
    {
        return $this->belongsTo('App\Models\Review');
    }

    public function attachments()
    {
        return $this->hasOne('App\Models\File', 'document_id', 'attachments_id');
    }

    public function approve()
    {
        $this->active = 1;

        $recipient = ($this->review->user_id == $this->user_id) ? $this->review->listing->user : $this->review->user;

        $emailData = [
            'sender_id' => $this->user->id,
            'sender_first_name' => $this->user->first_name,
            'sender_last_name' => $this->user->last_name,
            'sender_email' => $this->user->email,

            'recipient_id' => $recipient->id,
            'recipient_first_name' => $recipient->first_name,
            'recipient_last_name' => $recipient->last_name,
            'recipient_email' => $recipient->email,

            'listing_id' => $this->review->listing->id,
            'listing_title' => $this->review->listing->title,
            'listing_type_singular' => $this->review->type->name_singular,
            'listing_type_plural' => $this->review->type->name_plural,

            'review_id' => $this->review->id,
            'review_title' => $this->review->title,
            'review_description' => $this->review->description,

            'comment_id' => $this->id,
            'comment_description' => $this->description,

            'link' => route('account/review/' . $this->review->id),
        ];

        if (null !== $this->review->type->get('active')) {
            (new \App\Repositories\EmailQueue())->push(
                'user_comment_approved',
                $this->user->id,
                $emailData,
                [$this->user->email => $this->user->getName()],
                [config()->email->from_email => config()->email->from_name]
            );

            (new \App\Repositories\EmailQueue())->push(
                'user_comment_created',
                $recipient->id,
                $emailData,
                [$recipient->email => $recipient->getName()],
                [config()->email->from_email => config()->email->from_name]
            );
        }

        return $this;    
    }

    public function disapprove()
    {
        $this->active = null;

        return $this;
    }

}
