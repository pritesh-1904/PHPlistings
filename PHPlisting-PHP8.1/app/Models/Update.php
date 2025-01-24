<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class Update
    extends \App\Src\Orm\Model
{

    protected $table = 'updates';
    protected $fillable = [
        'type_id',
    ];
    protected $allowed = [
        'title',
        'slug',
        'short_description',
        'description',
        'event_start_datetime',
        'event_frequency',
        'event_interval',
        'event_weekdays',
        'event_weeks',
        'event_dates',
        'event_end_datetime',
        'event_rsvp',
        'offer_start_datetime',
        'offer_end_datetime',
        'offer_price',
        'offer_discount_type',
        'offer_discount',
        'offer_count',
        'offer_terms',
        'offer_redeem',
        'address',
        'zip',
        'location_id',
        'latitude',
        'longitude',
        'zoom',
        'timezone',
        'meta_title',
        'meta_keywords',
        'meta_description',
    ];

    public function parents()
    {
        return $this->belongsToMany(self::class, 'parent_id', 'child_id', 'linked_update');
    }

    public function listing()
    {
        return $this->belongsTo('App\Models\Listing');
    }

    public function data()
    {
        return $this->hasMany('App\Models\UpdateFieldData');
    }

    public function categories()
    {
        return $this->belongsToMany('App\Models\Category');
    }

    public function getTree($typeId, $userId = null)
    {
        $tree = [];

        $query = $this->getQuery();

        if (null !== $userId) {
            $query = \App\Models\User::find($userId)->listings();
        }

        foreach ($query->where('type_id', $typeId)->get() as $listing) {
            $tree[] = ['key' => $listing->id, 'title' => $listing->title];
        }

        return $tree;
    }

    public function import(\App\Models\Listing $listing)
    {
        $this->forceFill([
            'listing_id' => $listing->id,
            'added_datetime' => date('Y-m-d H:i:s'),
            'type_id' => $listing->type_id,
            'category_id' => $listing->category_id,
        ]);

        foreach ($listing->getFormFields('update', ['pricing_id' => $listing->order->pricing_id]) as $field) {
            if (null === $field->customizable) {
                $this->setFillable($field->name);
            }
        }

        return $this;
    }

    public function export()
    {
        foreach ($this->listing->getFormFields('update', ['pricing_id' => $this->listing->order->pricing_id]) as $field) {
            if (null === $field->customizable) {
                $this->listing->setFillable($field->name);
            }
        }

        $this->listing->fill($this->toArray());

        return $this->listing;
    }

    public function saveWithData(\App\Src\Support\Collection $input)
    {
        if (null !== $this->listing->update) {
            $this->listing->update->delete();
        }
        
        $data = [];

        $result = $this->save();

        foreach ($this->listing->getAllFormFields() as $field) {
            if (null !== $field->customizable) {
                $existing = $this->listing->data->where('field_name', $field->name)->first();
                $value = (false === $input->has($field->name) ? (null !== $existing ? $existing->value : null) : $input->get($field->name));

                if (in_array($field->type, ['dropzone', 'hours']) && '' == $value) {
                    $value = bin2hex(random_bytes(16));
                }

                $data[] = [
                    'active' => (null !== $existing ? $existing->active : null),
                    'update_id' => $this->id,
                    'field_name' => $field->name,
                    'value' => $value,
                ];
            }
        }

        $this->data()->delete();

        if (count($data) > 0) {
            db()->table('updatefielddata')->insert($data);
        }
        
        return $result;
    }

    public function delete($id = null)
    {
        $this->data()->delete();

        $this->categories()->detach();
        $this->parents()->detach();

        return parent::delete($id);
    }

}
