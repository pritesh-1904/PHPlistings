<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class User
    extends \App\Src\Orm\Model
{

    protected $table = 'users';
    protected $searchable = [
        'keyword' => [['first_name', 'last_name'], 'fulltext', false],
    ];
    protected $sortable = [
        'newest' => ['id', 'desc'],
        'oldest' => ['id', 'asc'],
        'id' => ['id'],
        'last_name' => ['last_name'],
        'active' => ['active'],
        'email_verified' => ['email_verified'],
    ];

    public function getName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getNameWithId()
    {
        return $this->getName() . ' (id:' . $this->id . ')';
    }

    public function data()
    {
        return $this->hasMany('App\Models\UserFieldData');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Location');
    }

    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }

    public function listings()
    {
        return $this->hasMany('App\Models\Listing');
    }

    public function claims()
    {
        return $this->hasMany('App\Models\Claim');
    }

    public function bookmarks()
    {
        return $this->belongsToMany('App\Models\Listing', 'listing_id', 'user_id', 'bookmarks');
    }

    public function reviews()
    {
        return $this->hasMany('App\Models\Review');
    }

    public function invoices()
    {
        return $this->hasMany('App\Models\Invoice');
    }

    public function inbox()
    {
        return $this->hasMany('App\Models\Message', 'recipient_id', 'id');
    }

    public function outbox()
    {
        return $this->hasMany('App\Models\Message', 'sender_id', 'id');
    }

    public function emails()
    {
        return $this->hasMany('App\Models\Email', 'recipient_id', 'id');
    }

    public function reminders()
    {
        return $this->hasMany('App\Models\Reminder');
    }

    public function account()
    {
        return $this->hasOne('App\Models\Account');
    }

    public function getAllFormFields()
    {
        if (!cache()->collection()->has('user-form-fields')) {
            cache()->collection()->put('user-form-fields', 
                \App\Models\FieldGroup::find(1)
                    ->fields()
                    ->get()
            );
        }

        return cache()->collection()->get('user-form-fields');
    }
    
    public function getOutputableForm()
    {
        $name = 'users-' . locale()->getLocale() . '-outputable-form';

        if (!cache()->collection()->has($name)) {
            $fields = \App\Models\FieldGroup::find(1)
                ->fields()
                ->where('outputable', 1)
                ->orderBy('weight')
                ->with([
                    'options',
                ])
                ->get();

            cache()->collection()->put($name, 
                form()->importWithoutConstraints($fields)
            );
        }

        return cache()->collection()->get($name);
    }

    public function getFormFields($type = 'submit')
    {
        $group = \App\Models\FieldGroup::find(1);
        
        if (null !== $group) {
            $query = $group->fields();

            if ($type == 'submit') {
                $query->where('submittable', 1);
            } else if ($type == 'update') {
                $query->where('updatable', 1);
            }

            return $query
                ->with([
                    'options',
                    'constraints',
                ])
                ->get();
        }
    }

    public function performInsert()
    {
        $this->token = bin2hex(random_bytes(16));
        $this->verification_code = bin2hex(random_bytes(16));

        return parent::performInsert();
    }

    public function saveWithData(\App\Src\Support\Collection $input)
    {
        $data = [];
        $exists = $this->exists;

        $result = $this->save();

        foreach ($this->getAllFormFields() as $field) {
            if (null !== $field->customizable) {
                $existing = (false !== $exists) ? $this->data->where('field_name', $field->name)->first() : null;
                $value = (false === $input->has($field->name) ? (null !== $existing ? $existing->value : null) : $input->get($field->name));

                if (in_array($field->type, ['dropzone', 'hours']) && ('' == $value || null === $value)) {
                    $value = bin2hex(random_bytes(16));
                }

                $data[] = [
                    'user_id' => $this->id,
                    'field_name' => $field->name,
                    'value' => $value,
                ];
            }
        }

        $this->data()->delete();

        if (count($data) > 0) {
            db()->table('userfielddata')->insert($data);
        }
        
        return $result;
    }

    public function delete($id = null)
    {
        foreach ($this->listings()->get() as $listing) {
            $listing->delete();
        }

        $this->claims()->delete();

        db()->table('bookmarks')
            ->where('user_id', $this->id)
            ->delete();

        foreach ($this->reviews()->get() as $review) {
            $review->delete();
        }

        db()->table('comments')->where('user_id', $this->id)->delete();

        foreach ($this->outbox()->get() as $review) {
            $review->delete();
        }

        db()->table('replies')->where('user_id', $this->id)->delete();

        db()->table('emails')->where('recipient_id', $this->id)->delete();

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

        $this->account()->delete();

        return parent::delete($id);
    }

    public function isOnline()
    {
        return ((new \DateTime('now', new \DateTimeZone('+0000')))->getTimestamp() - (new \DateTime($this->account->last_activity_datetime, new \DateTimeZone('+0000')))->getTimestamp() <= 300);
    }

    public function approve()
    {
        $this->active = 1;

        (new \App\Repositories\EmailQueue())->push(
            'user_account_approved',
            $this->id,
            [
                'id' => $this->id,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'link' => route('account/login'),
            ],
            [$this->email => $this->getName()],
            [config()->email->from_email => config()->email->from_name]
        );

        return $this;
    }

    public function disapprove()
    {
        $this->active = null;

        return $this;
    }

    public function setSearchable($attribute, $type, $external = false)
    {
        if (!array_key_exists($attribute, $this->searchable)) {
            $this->searchable[$attribute] = [$attribute, $type, $external];
        }

        return $this;        
    }

    public static function search(\App\Src\Orm\Model $bindModel = null, array $options = [], $identifier = null)
    {
        if (null !== $bindModel) {
            $query = $bindModel->getQuery();
        } else {
            $query = (new static())->newQuery();
        }

        $identifier = $identifier ?? getRoute();

        $prefixedTable = $query->getModel()->getPrefixedTable();

        if (session()->has($identifier)) {
            session()->forget($identifier);
        }

        if (request()->get->count() > 0) {
            session()->put($identifier, request()->get->all());
        }

        $fields = $query->getModel()->getFormFields('search');

        foreach ($fields as $field) {
            if ($field->queryable) {
                if (in_array($field->type, [
                    'captcha',
                    'color',
                    'dropzone',
                    'hidden',
                    'hours',
                    'password',
                    'ro',
                    'separator',
                    'youtube',
                ])) {
                    continue;
                }
                
                if (in_array($field->type, [
                    'date',
                    'dates',
                    'datetime',
                    'time',
                ])) {
                    $field->search_type = $field->type;
                }

                if ($field->search_type == 'range' && false === in_array($field->type, ['number', 'price'])) {
                    $field->search_type = 'eq';
                }

                if (false !== in_array($field->type, ['checkbox', 'mselect'])) {
                    $field->search_type = ($field->search_type == 'eq') ? 'all' : 'any';
                }
                
                if ($field->type == 'select') {
                    $field->search_type = 'eq';
                }

                if ($field->type == 'keywords') {
                    $field->search_type = 'keywords';
                }

                $query->getModel()->setSearchable($field->name, $field->search_type, (null !== $field->customizable));
            }

            if ($field->sortable) {
                $query->getModel()->setSortable($field->name, $field->name);
            }
        }

        foreach (request()->get as $parameter => $value) {
            if ('' === $value) {
                continue;
            }                       
  
            if (array_key_exists($parameter, $query->getModel()->searchable)) {
                list($column, $expression, $external) = $query->getModel()->searchable[$parameter];

                    if (false !== $external) {
                        $query->leftJoin(
                            db()->getPrefix() . 'userfielddata',
                            db()->raw($prefixedTable . '.id = ' . $column . '.user_id AND ' . $column . '.field_name = ?', [$column]),
                            $column
                        );
                    }

                switch ($expression) {
                    case 'location':
                        if (isset(request()->get->radius) && is_numeric(request()->get->radius) && request()->get->radius > 0 && request()->get->radius <= 100) {
                            if (null !== $location = \App\Models\Location::find($value)) {
                                $query
                                    ->select(db()->raw('(
                                        ' . (isset($options['distance_type']) && $options['distance_type'] == 'kilometers' ? '6371' : '3959') . ' * acos (
                                            cos ( radians(?) )
                                            * cos ( radians (' . $prefixedTable . '.latitude) )
                                            * cos ( radians (' . $prefixedTable . '.longitude) - radians (?) )
                                            + sin ( radians (?) )
                                            * sin ( radians (' . $prefixedTable . '.latitude) )
                                            )
                                        ) AS _distance
                                    ', [
                                        $location->latitude,
                                        $location->longitude,
                                        $location->latitude
                                    ]))
                                    ->having(
                                        db()->raw(
                                            '_distance <= ?',
                                            [(int) request()->get->radius]
                                        )
                                    );
            
                                $query->getModel()->setSortable('distance', '_distance');
                            }
                        } else {
                            $location = \App\Models\Location::find($value);
                            if (null !== $location) {
                                $query->whereIn($prefixedTable . '.location_id', $location->descendantsAndSelf()->get()->pluck('id')->all());
                            }
                        }
                        break;
                    case 'fulltext':
                        if (is_array($column)) {
                            $column = array_map(function ($value) use ($prefixedTable) { return $prefixedTable . '.' . $value; }, $column);
                        } else {
                            $column = $prefixedTable . '.' . $column;
                        }

                        $query
                            ->select(
                                db()->raw(
                                    'MATCH(' . implode(', ', (array) $column) . ') AGAINST(?) AS _score',
                                    [$value]
                                )
                            )
                            ->where(
                                db()->raw(
                                    '(MATCH(' . implode(', ', (array) $column) . ') AGAINST(?)) > 0.1',
                                    [$value]
                                )
                            );

                        $query->getModel()->setSortable('relevance', '_score');

                        break;
                    case 'null':
                        if (false === $external) {
                            $query->whereNull($prefixedTable . '.' . $column);
                        } else {
                            $query->where(function($query) {
                                $query
                                    ->whereNull($column . '.value');
                            });
                        }
                        break;
                    case 'like':
                        $value = '%' . $value . '%';
                    case 'eq':
                        if (false === $external) {
                            if (null === $value) {
                                $query->whereNull($prefixedTable . '.' . $column);
                            } else {
                                $query->where($prefixedTable . '.' . $column, $expression, $value);
                            }
                        } else {
                            $query->where(function($query) use ($column, $expression, $value) {
                                if (null === $value) {
                                    $query->whereNull($column . '.value');
                                } else { 
                                    $query->where($column . '.value', $expression, $value);
                                }
                            });
                        }
                        break;
                    case 'keywords':
                        $value = array_map('trim', explode(',', $value));
                    case 'all':
                        $query->where(
                            function ($query) use ($value, $column, $external, $prefixedTable) {
                                if (false === $external) {
                                    foreach ($value as $item) {
                                        $query->where(db()->raw('FIND_IN_SET(?, ' . $prefixedTable . '.' . $column . ') > 0', [$item]));
                                    }
                                } else {
                                    $query
                                        ->where(function ($query) use ($value, $column) {
                                            foreach ($value as $item) {
                                                $query->where(db()->raw('FIND_IN_SET(?, ' . $column . '.value) > 0', [$item]));
                                            }
                                        });
                                }
                            });
                        break;
                    case 'any':
                        $query->where(
                            function ($query) use ($value, $column, $external, $prefixedTable) {
                                if (false === $external) {
                                    foreach ($value as $item) {
                                        $query->orWhere(db()->raw('FIND_IN_SET(?, ' . $prefixedTable . '.' . $column . ') > 0', [$item]));
                                    }
                                } else {
                                    $query
                                        ->where(function ($query) use ($column, $value) {
                                            foreach ($value as $item) {
                                                $query->orWhere(db()->raw('FIND_IN_SET(?, ' . $column . '.value) > 0', [$item]));
                                            }
                                        });
                                }
                            });
                        break;
                    case 'range':
                        if (strstr($value, ';')) {
                            if (count(explode(';', $value)) == 2) {
                                list($min, $max) = explode(';', $value);
                                if (false === $external) {
                                    $query->where($query->raw('(CAST(' . $prefixedTable . '.' . $column . ' AS SIGNED INTEGER) >= ? AND CAST(' . $prefixedTable . '.' . $column . ' AS SIGNED INTEGER) <= ?)', [$min, $max]));
                                } else {
                                    $query->where(function ($query) use ($min, $max, $column) {
                                        $query
                                            ->where(db()->raw('(CAST(' . $column . '.value AS SIGNED INTEGER) >= ? AND CAST(' . $column . '.value AS SIGNED INTEGER) <= ?)', [$min, $max]));
                                    });
                                }
                            }
                        }
                        break;
                    case 'date':
                        if (null !== $value && '' !== $value) {
                            try {
                                $value = (new \App\Src\DataTransformer\LocalizedStringToDate(locale()->getDateFormat()))->transform($value);
                            } catch (\App\DataTranformer\FailedTransformationException $e) {
                                $value = '';
                            }

                            $query->where($column . (false !== $external ? '.value' : ''), $value);
                        }
                        break;
                    case 'dates':
                        if (null !== $value && '' !== $value) {
                            try {
                                $value = (new \App\Src\DataTransformer\LocalizedStringToDates(locale()->getDateFormat()))->transform($value);
                                $dates = array_map('trim', explode(',', $value));
                            } catch (\App\DataTranformer\FailedTransformationException $e) {
                                $dates = [date('Y-m-d')];
                            }

                            $query->whereIn('dates.event_date', $dates);
                        }
                        break;
                    case 'datetime':
                        if (null !== $value && '' !== $value) {
                            try {
                                $value = (new \App\Src\DataTransformer\LocalizedStringToDatetime(locale()->getDateFormat() . ' ' . locale()->getTimeFormat()))->transform($value);
                            } catch (\App\DataTranformer\FailedTransformationException $e) {
                                $value = '';
                            }

                            $query->where($column . (false !== $external ? '.value' : ''), $value);
                        }
                        break;
                    case 'time':
                        if (null !== $value && '' !== $value) {
                            try {
                                $value = (new \App\Src\DataTransformer\LocalizedStringToTime(locale()->getTimeFormat()))->transform($value);
                            } catch (\App\DataTranformer\FailedTransformationException $e) {
                                $value = '';
                            }

                            $query->where($column . (false !== $external ? '.value' : ''), $value);
                        }
                        break;
                }
            }
        }

        if (null !== request()->get->get('sort') && '' != request()->get->get('sort') && isset($query->getModel()->sortable[request()->get->get('sort')][0])) {
            $column = $query->getModel()->sortable[request()->get->sort][0];

            $direction = 'asc';

            if (null !== request()->get->get('sort_direction') && in_array(request()->get->get('sort_direction'), ['asc', 'desc'])) {
                $direction = request()->get->get('sort_direction');
            }

            if (isset($query->getModel()->sortable[request()->get->sort][1])) {
                $direction = $query->getModel()->sortable[request()->get->sort][1];
            }

            $query->orderBy($column, $direction);
        }

        return $query;
    }

}
