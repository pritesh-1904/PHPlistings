<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class Review
    extends \App\Src\Orm\Model
{

    protected $table = 'reviews';
    protected $fillable = [
        'rating',
        'title',
        'description',
        'attachments_id',
    ];
    protected $sortable = [
        'id' => ['id'],
        'title' => ['title'],
        'added_datetime' => ['added_datetime'],
    ];
    protected $searchable = [
        'user_id' => ['user_id', 'eq'],
        'listing_id' => ['listing_id', 'eq'],
    ];

    public function type()
    {
        return $this->belongsTo('App\Models\Type');
    }

    public function data()
    {
        return $this->hasMany('App\Models\ReviewFieldData');
    }

    public function listing()
    {
        return $this->belongsTo('App\Models\Listing');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Comment');
    }

    public function attachments()
    {
        return $this->hasOne('App\Models\File', 'document_id', 'attachments_id');
    }

    public function getOutputableValue($name, $length = 150)
    {        
        if ($name == '_rating') {
            return view('misc/rating', [
                'rating' => $this->rating,
            ]);
        } else if ($name == '_description') {
            return mb_strimwidth(e($this->description), 0, $length, '...');
        }
    }

    public function changeRecipient($oldId, $newId)
    {
        $this->comments()->where('user_id', $oldId)->update(['user_id' => $newId]);
        
        return $this;
    }

    public function getOutputableForm($search = false)
    {
        $name = 'reviews-' . $this->type_id . '-' . locale()->getLocale() . '-outputable-form';

        if (!cache()->collection()->has($name)) {
            $query = (new \App\Models\ListingField())
                ->where('listingfieldgroup_id', 2)
                ->where((false === $search ? 'outputable' : 'outputable_search'), 1)
                ->where('type_id', $this->listing->type_id)
                ->whereNotNull('customizable');

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
                ->with('options')
                ->orderBy('weight')
                ->get();

            cache()->collection()->put($name, 
                form()->importWithoutConstraints($fields)
            );
        }

        return cache()->collection()->get($name);
    }

    public function getOutputableSearchResultForm()
    {
        return $this->getOutputableForm(true);
    }

    public function getFormFields($type = 'submit')
    {
        $query = (new \App\Models\ListingField())
            ->where('listingfieldgroup_id', 2);

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

                    if (null !== $this->listing->order->pricing_id) {
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
                    'review_id' => $this->id,
                    'field_name' => $field->name,
                    'value' => $value,
                ];
            }
        }

        $this->data()->delete();

        if (count($data) > 0) {
            db()->table('reviewfielddata')->insert($data);
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

        $this->comments()->delete();

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
            'sender_id' => $this->user->id,
            'sender_first_name' => $this->user->first_name,
            'sender_last_name' => $this->user->last_name,
            'sender_email' => $this->user->email,

            'recipient_id' => $this->listing->user->id,
            'recipient_first_name' => $this->listing->user->first_name,
            'recipient_last_name' => $this->listing->user->last_name,
            'recipient_email' => $this->listing->user->email,

            'listing_id' => $this->listing->id,
            'listing_title' => $this->listing->title,
            'listing_type_singular' => $this->type->name_singular,
            'listing_type_plural' => $this->type->name_plural,

            'review_id' => $this->id,
            'review_title' => $this->title,
            'review_description' => $this->description,

            'link' => route('account/reviews/' . $this->id),
        ];

        if (null !== $this->type->get('active')) {
            (new \App\Repositories\EmailQueue())->push(
                'user_review_approved',
                $this->user->id,
                $emailData,
                [$this->user->email => $this->user->getName()],
                [config()->email->from_email => config()->email->from_name]
            );

            (new \App\Repositories\EmailQueue())->push(
                'user_review_created',
                $this->listing->user->id,
                $emailData,
                [$this->listing->user->email => $this->listing->user->getName()],
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
