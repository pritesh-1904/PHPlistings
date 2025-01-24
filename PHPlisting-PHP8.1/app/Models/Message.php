<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2022 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class Message
    extends \App\Src\Orm\Model
{

    protected $table = 'messages';
    protected $fillable = [
        'title',
        'description',
        'attachments_id',
    ];
    protected $searchable = [
        'listing_id' => ['listing_id', 'eq'],
    ];
    protected $sortable = [
        'id' => ['id'],
        'title' => ['title'],
        'added_datetime' => ['added_datetime'],
        'lastreply_datetime' => ['lastreply_datetime'],
    ];

    public function type()
    {
        return $this->belongsTo('App\Models\Type');
    }

    public function data()
    {
        return $this->hasMany('App\Models\MessageFieldData');
    }

    public function listing()
    {
        return $this->belongsTo('App\Models\Listing');
    }

    public function sender()
    {
        return $this->belongsTo('App\Models\User', 'id', 'sender_id');
    }

    public function recipient()
    {
        return $this->belongsTo('App\Models\User', 'id', 'recipient_id');
    }

    public function replies()
    {
        return $this->hasMany('App\Models\Reply');
    }

    public function attachments()
    {
        return $this->hasOne('App\Models\File', 'document_id', 'attachments_id');
    }

    public function changeRecipient($id)
    {
        if ($id != $this->recipient_id) {
            $this->replies()->where('user_id', $this->recipient_id)->update(['user_id' => $id]);

            $this->recipient_id = $id;
        }
        
        return $this;
    }
    
    public function getOutputableForm()
    {
        $name = 'messages-' . $this->type_id . '-' . locale()->getLocale() . '-outputable-form';

        if (!cache()->collection()->has($name)) {
            $query = (new \App\Models\ListingField())
                ->where('listingfieldgroup_id', 3)
                ->whereNotNull('customizable')
                ->where('type_id', $this->listing->type_id)
                ->where('outputable', 1)
                ->orderBy('weight');

                if (false !== $this->hasRelation('listing')) {
                    $query->where(function ($query) {
                        $query->where(function ($query) {
                            if (null !== $this->listing->category_id) {
                                $query->whereHas('categories', function ($relation) {
                                    $relation->where('category_id', $this->listing->category_id);
                                });
                            }

                            if (null !== $this->listing->pricing_id) {
                                $query->whereHas('products', function ($relation) {
                                    $relation->where('product_id', $this->listing->pricing->product->id);
                                });
                            }

                            $query->orWhereNull('customizable');
                        });
                    });
                }

            $fields = $query
                ->with(['options'])
                ->get();

            cache()->collection()->put($name, 
                form()->importWithoutConstraints($fields)
            );
        }

        return cache()->collection()->get($name);
    }

    public function getFormFields($type = 'submit')
    {
        $query = \App\Models\ListingFieldGroup::find(3)
            ->fields();

        if (null !== $this->get('type_id')) {
            $query
                ->where('type_id', $this->type_id);
        }

        if (false !== $this->hasRelation('listing')) {
            $query->where(function ($query) {
                $query->where(function ($query) {
                    if (null !== $this->listing->category_id) {
                        $query->whereHas('categories', function ($relation) {
                            $relation->where('category_id', $this->listing->category_id);
                        });
                    }

                    if (null !== $this->listing->pricing_id) {
                        $query->whereHas('products', function ($relation) {
                            $relation->where('product_id', $this->listing->order->pricing->product->id);
                        });
                    }

                    $query->orWhereNull('customizable');
                });
            });
        }
    
        return $query->orderBy('weight')->with(['options', 'constraints'])->get();
    }

    public function saveWithData(\App\Src\Support\Collection $input)
    {
        $data = [];
        $exists = $this->exists;

        $result = $this->save();

        foreach ($this->getFormFields() as $field) {
            if (null !== $field->customizable) {
                $existing = (false !== $exists) ? $this->data->where('field_name', $field->name)->first() : null;
                $value = (false === $input->has($field->name) ? (null !== $existing ? $existing->value : null) : $input->get($field->name));

                if (in_array($field->type, ['dropzone', 'hours']) && ('' == $value || null === $value)) {
                    $value = bin2hex(random_bytes(16));
                }

                $data[] = [
                    'message_id' => $this->id,
                    'field_name' => $field->name,
                    'value' => $value,
                ];
            }
        }

        $this->data()->delete();

        if (count($data) > 0) {
            db()->table('messagefielddata')->insert($data);
        }
        
        return $result;
    }

    public function delete($id = null)
    {
        if (null !== $this->attachments_id && '' != $this->attachments_id && null !== $this->attachments) {
            foreach ($this->attachments as $attachment) {
                $attachment->delete();
            }
        }

        $this->replies()->delete();

        foreach ($this->getFormFields() as $field) {
            if (in_array($field->type, ['dropzone', 'hours'])) {
                if (null === $field->customizable) {
                    $value = $this->get($field->name);
                } else if (null !== $this->data()->where('field_name', $field->name)->first()) {
                    $value = $this->data()->where('field_name', $field->name)->first()->value;
                } else {
                    $value = null;
                }

                if (null !== $value && '' != $value) {
                    if ('dropzone' == $field->type) {
                        if (null !== $files = \App\Models\File::where('document_id', $value)->get()) {
                            foreach ($files as $file) {
                                $file->delete();
                            }
                        }
                    } else if ('hours' == $field->type) {
                        if (null !== $hours = \App\Models\Hour::where('hash', $value)->get()) {
                            foreach ($hours as $hour) {
                                $hour->delete();
                            }
                        }
                    }
                }
            }
        }

        $this->data()->delete();

        return parent::delete($id);
    }

    public function approve()
    {
        $this->active = 1;

        $emailData = [
            'sender_id' => $this->sender->id,
            'sender_first_name' => $this->sender->first_name,
            'sender_last_name' => $this->sender->last_name,
            'sender_email' => $this->sender->email,

            'recipient_id' => $this->recipient->id,
            'recipient_first_name' => $this->recipient->first_name,
            'recipient_last_name' => $this->recipient->last_name,
            'recipient_email' => $this->recipient->email,

            'listing_id' => $this->listing->id,
            'listing_title' => $this->listing->title,
            'listing_type_singular' => $this->type->name_singular,
            'listing_type_plural' => $this->type->name_plural,

            'message_id' => $this->id,
            'message_title' => $this->title,
            'message_description' => $this->description,

            'link' => route('account/messages/' . $this->id),
        ];

        if (null !== $this->type->get('active')) {
            (new \App\Repositories\EmailQueue())->push(
                'user_message_approved',
                $this->sender->id,
                $emailData,
                [$this->sender->email => $this->sender->getName()],
                [config()->email->from_email => config()->email->from_name]
            );

            (new \App\Repositories\EmailQueue())->push(
                'user_message_created',
                $this->recipient->id,
                $emailData,
                [$this->recipient->email => $this->recipient->getName()],
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
