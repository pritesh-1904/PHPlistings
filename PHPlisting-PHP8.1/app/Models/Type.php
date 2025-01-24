<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2024 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class Type
    extends \App\Src\Orm\Model
{
    use \App\Src\Orm\Traits\SortableTrait;

    protected $table = 'types';
    protected $fillable = [
        'active',
        'approvable',
        'approvable_updates',
        'approvable_reviews',
        'approvable_comments',
        'approvable_messages',
        'approvable_replies',
        'localizable',
        'reviewable',
        'type',
        'name_singular',
        'name_plural',
        'slug',
        'icon',
        'peruser_limit',
        'address_format',
        'schematype_id',
    ];
    protected $translatable = [
        'name_singular',
        'name_plural',
    ];

    public function listings()
    {
        return $this->hasMany('App\Models\Listing');
    }

    public function badges()
    {
        return $this->hasMany('App\Models\Badge');
    }

    public function fields()
    {
        return $this->hasMany('App\Models\ListingField');
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product');
    }

    public function categories()
    {
        return $this->hasMany('App\Models\Category');
    }

    public function reviews()
    {
        return $this->hasMany('App\Models\Review');
    }

    public function parents()
    {
        return $this->belongsToMany('App\Models\Type', 'parent_id', 'child_id', 'type_linked');
    }

    public function children()
    {
        return $this->belongsToMany('App\Models\Type', 'child_id', 'parent_id', 'type_linked');
    }

    public function ratings()
    {
        return $this->belongsToMany('App\Models\Rating');
    }

    public function schemaType()
    {
        return $this->belongsTo('App\Models\SchemaType');
    }

    public function exports()
    {
        return $this->hasMany('App\Models\Export');
    }

    public function imports()
    {
        return $this->hasMany('App\Models\Import');
    }

    public function isPrimary()
    {
        if (false !== $this->exists) {
            if (null !== $type = \App\Models\Type::whereNull('deleted')->whereNotNull('active')->orderBy('weight')->first(['id'])) {
                return (bool) ($type->id == $this->id);
            }
        }

        return false;
    }

    public function getTree($exclude = null)
    {
        $tree = [];

        foreach ($this->getQuery()->whereNull('deleted')->get() as $type) {
            if ($exclude !== $type->id) {
                $tree[] = ['key' => $type->id, 'title' => $type->name_plural];
            }
        }

        return $tree;
    }

    public function getDropdownTree($exclude = null)
    {
        $query = $this->getQuery()
            ->whereNull('deleted');
        
        if (null !== $exclude) {
            $query
                ->where($this->getPrimaryKey(), '!=', $exclude);
        }
        
        return $query
            ->get()
            ->pluck('name', 'id')
            ->all();
    }

    public function performInsert()
    {
        $this->weight = (int) $this->newQuery()->max('weight') + 1;

        $result = parent::performInsert();

        if (false !== $result) {
            $widgets = \App\Models\Widget::all();

            $pages = \App\Models\Page::query()
                ->with('widgets', function ($query) {
                    $query->withPivot(['id', 'weight', 'settings']);
                })
                ->get();

            foreach (config()->themes->pages->type as $key => $value) {
                $page = new \App\Models\Page();
                $page->active = 1;
                $page->slug = 'type/' . $key;
                $page->type_id = $this->id;
                $page->setTranslation('title', $value->title, 'en');
                $page->save();

                foreach (config()->themes->pages->type->get($key)->widgets as $slug) {
                    if (null !== $widget = $widgets->where('slug', $slug)->first()) {
                        $settings = (new \App\Src\DataTransformer\ArrayToJson())->transform($widget->getWidgetObject()->getDefaultSettings()->all());

                        if (false === $widget->getWidgetObject()->isMultiInstance()) {
                            foreach ($pages as $page2) {
                                foreach ($page2->widgets as $widget2) {
                                    if ($widget->slug == $widget2->slug) {
                                        $settings = $widget2->pivot->settings;
                                    }
                                }
                            }
                        }

                        $weight = (int) db()->table('page_widget')->max('weight') + 1;

                        $page->widgets()->attach($widget->id, ['active' => 1, 'weight' => $weight, 'settings' => $settings, 'access_level' => 1]);
                    }
                }
            }        

            $root = new \App\Models\Category();
            $root->type_id = $this->get($this->getPrimaryKey());
            $root->setTranslation('name', 'ROOT', 'en');
            $root->slug = 'root';
            $root->setRoot()->save();
            
            $fields = [
                [
                    'customizable' => 1,
                    'type' => 'dropzone',
                    'name' => 'logo_id',
                    'label' => 'Logo',
                    'upload_id' => 1,
                    'schema_itemprop' => 'logo',
                    'constraints' => [],
                ],
                [
                    'type' => 'text',
                    'name' => 'title',
                    'label' => 'Title',
                    'schema_itemprop' => 'name',
                    'constraints' => [
                        ['name' => 'required'],
                        [
                            'name' => 'bannedwords',
                            'customizable' => 1,
                        ],
                    ],
                ],
                [
                    'type' => 'text',
                    'name' => 'slug',
                    'label' => 'Slug',
                    'sluggable' => 'title',
                    'constraints' => [
                        ['name' => 'required'],
                        ['name' => 'alphanumericdash'],
                        [
                            'name' => 'maxlength',
                            'value' => '120',
                            'customizable' => 1,
                        ],
                    ],
                ],
                [
                    'type' => 'text',
                    'name' => 'short_description',
                    'label' => 'Summary',
                    'constraints' => [
                        [
                            'name' => 'bannedwords',
                            'customizable' => 1,
                        ],
                    ],
                ],
                [
                    'type' => 'htmltextarea',
                    'name' => 'description',
                    'label' => 'Description',
                    'schema_itemprop' => 'description',
                    'constraints' => [
                        [
                            'name' => 'bannedwords',
                            'customizable' => 1,
                        ],
                    ],
                ],
                [
                    'customizable' => 1,
                    'type' => 'dropzone',
                    'name' => 'gallery_id',
                    'label' => 'Gallery',
                    'upload_id' => 2,
                ],
                [
                    'customizable' => 1,
                    'type' => 'hours',
                    'name' => 'opening_hours_id',
                    'label' => 'Opening Hours',
                ],
                [
                    'customizable' => 1,
                    'type' => 'phone',
                    'name' => 'phone',
                    'label' => 'Phone',
                    'constraints' => [],
                ],
                [
                    'customizable' => 1,
                    'type' => 'url',
                    'name' => 'website',
                    'label' => 'Website',
                    'constraints' => [],
                ],
            ];

            if ('Event' == $this->type) {
                $fields = array_merge($fields, [
                    [
                        'type' => 'datetime',
                        'name' => 'event_start_datetime',
                        'label' => 'Event Start Date',
                        'schema_itemprop' => 'startDate',
                        'constraints' => [
                            ['name' => 'required'],
                        ],
                    ],
                    [
                        'type' => 'select',
                        'name' => 'event_frequency',
                        'label' => 'Frequency',
                        'constraints' => [
                            ['name' => 'required'],
                        ],
                        'options' => [
                            ['name' => 'once', 'value' => 'Once'],
                            ['name' => 'daily', 'value' => 'Daily'],
                            ['name' => 'weekly', 'value' => 'Weekly'],
                            ['name' => 'monthly', 'value' => 'Monthly'],
                            ['name' => 'yearly', 'value' => 'Yearly'],
                            ['name' => 'custom', 'value' => 'Custom'],
                        ]
                    ],
                    [
                        'type' => 'number',
                        'name' => 'event_interval',
                        'label' => 'Interval',
                        'constraints' => [
                            ['name' => 'required'],
                            ['name' => 'min', 'value' => '1'],
                            ['name' => 'max', 'value' => '31'],
                        ],
                        'value' => '1',
                    ],
                    [
                        'type' => 'checkbox',
                        'name' => 'event_weekdays',
                        'label' => 'Weekdays',
                        'constraints' => [],
                        'options' => [
                            ['name' => '7', 'value' => 'Sunday'],
                            ['name' => '1', 'value' => 'Monday'],
                            ['name' => '2', 'value' => 'Tuesday'],
                            ['name' => '3', 'value' => 'Wednesday'],
                            ['name' => '4', 'value' => 'Thursday'],
                            ['name' => '5', 'value' => 'Friday'],
                            ['name' => '6', 'value' => 'Saturday'],
                        ]
                    ],
                    [
                        'type' => 'checkbox',
                        'name' => 'event_weeks',
                        'label' => 'Weeks',
                        'constraints' => [],
                        'options' => [
                            ['name' => '1', 'value' => 'First'],
                            ['name' => '2', 'value' => 'Second'],
                            ['name' => '3', 'value' => 'Third'],
                            ['name' => '4', 'value' => 'Fourth'],
                            ['name' => '5', 'value' => 'Last'],
                        ]
                    ],
                    [
                        'type' => 'dates',
                        'name' => 'event_dates',
                        'label' => 'Event Custom Dates',
                        'constraints' => [],
                    ],
                    [
                        'type' => 'datetime',
                        'name' => 'event_end_datetime',
                        'label' => 'Event End Date',
                        'schema_itemprop' => 'endDate',
                        'constraints' => [
                            ['name' => 'required'],
                        ],
                    ],
                    [
                        'type' => 'toggle',
                        'name' => 'event_rsvp',
                        'label' => 'Allow RSVPs',
                    ],
                ]);
            }

            if ('Offer' == $this->type) {
                $fields = array_merge($fields, [
                    [
                        'type' => 'datetime',
                        'name' => 'offer_start_datetime',
                        'label' => 'Offer Start Date',
                        'schema_itemprop' => 'validFrom',
                        'constraints' => [
                            ['name' => 'required'],
                        ],
                    ],
                    [
                        'type' => 'datetime',
                        'name' => 'offer_end_datetime',
                        'label' => 'Offer End Date',
                        'schema_itemprop' => 'validThrough',
                        'constraints' => [
                            ['name' => 'required'],
                        ],
                    ],
                    [
                        'type' => 'price',
                        'name' => 'offer_price',
                        'label' => 'Item Price',
                    ],
                    [
                        'type' => 'select',
                        'name' => 'offer_discount_type',
                        'label' => 'Discount Type',
                        'constraints' => [
                            ['name' => 'required'],
                        ],
                        'options' => [
                            ['name' => 'fixed', 'value' => 'Fixed Value'],
                            ['name' => 'percentage', 'value' => 'Percentage'],
                        ]
                    ],
                    [
                        'type' => 'number',
                        'name' => 'offer_discount',
                        'label' => 'Discount Value',
                        'value' => '0',
                        'constraints' => [
                            ['name' => 'required'],
                            ['name' => 'min', 'value' => '0'],
                        ],
                    ],
                    [
                        'type' => 'number',
                        'name' => 'offer_count',
                        'label' => 'Available Offers',
                        'value' => '0',
                        'constraints' => [
                            ['name' => 'required'],
                            ['name' => 'min', 'value' => '0'],
                        ],
                    ],
                    [
                        'type' => 'textarea',
                        'name' => 'offer_terms',
                        'label' => 'Offer Terms and Conditions',
                        'constraints' => [
                            [
                                'name' => 'maxlength',
                                'value' => '500',
                                'customizable' => 1,
                            ],
                            [
                                'name' => 'bannedwords',
                                'customizable' => 1,
                            ],
                        ],
                    ],
                    [
                        'type' => 'toggle',
                        'name' => 'offer_redeem',
                        'label' => 'Allow Redeeming',
                    ],
                ]);
            }

            if (null !== $this->localizable) {
                $fields = array_merge($fields, [
                    [
                        'type' => 'text',
                        'name' => 'address',
                        'label' => 'Address',
                        'schema_itemprop' => 'streetAddress',
                        'constraints' => [
                            [
                                'name' => 'required',
                                'customizable' => 1,
                            ],
                            [
                                'name' => 'bannedwords',
                                'customizable' => 1,
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'name' => 'zip',
                        'label' => 'Zip / Postal Code',
                        'schema_itemprop' => 'postalCode',
                        'constraints' => [
                            [
                                'name' => 'bannedwords',
                                'customizable' => 1,
                            ],
                        ],
                    ],
                    [
                        'type' => 'locationmappicker',
                        'name' => 'location_id',
                        'label' => 'Location',
                        'constraints' => [
                            ['name' => 'required'],
                            ['name' => 'number'],
                            ['name' => 'isleaf', 'value' => 'locations'],
                        ],
                    ],
                    [
                        'type' => 'number',
                        'name' => 'latitude',
                        'label' => 'Latitude',
                        'schema_itemprop' => 'latitude',
                        'constraints' => [
                            ['name' => 'required'],
                            ['name' => 'min', 'value' => '-90'],
                            ['name' => 'max', 'value' => '90'],
                        ],
                    ],
                    [
                        'type' => 'number',
                        'name' => 'longitude',
                        'label' => 'Longitude',
                        'schema_itemprop' => 'longitude',
                        'constraints' => [
                            ['name' => 'required'],
                            ['name' => 'min', 'value' => '-180'],
                            ['name' => 'max', 'value' => '180'],
                        ],
                    ],
                    [
                        'type' => 'hidden',
                        'name' => 'zoom',
                        'label' => 'Zoom',
                        'value' => 15,
                        'constraints' => [
                            ['name' => 'required'],
                            ['name' => 'number'],
                            ['name' => 'min', 'value' => '0'],
                            ['name' => 'max', 'value' => '20'],
                        ],
                    ],
                ]);
            }

            $fields = array_merge($fields, [
                [
                    'type' => 'text',
                    'name' => 'meta_title',
                    'label' => 'SEO Title',
                    'constraints' => [
                        [
                            'name' => 'bannedwords',
                            'customizable' => 1,
                        ],
                        [
                            'name' => 'maxlength',
                            'value' => '255',
                            'customizable' => 1,
                        ],
                    ],
                ],
                [
                    'type' => 'keywords',
                    'name' => 'meta_keywords',
                    'label' => 'SEO Keywords',
                    'constraints' => [
                        [
                            'name' => 'bannedwords',
                            'customizable' => 1,
                        ],
                    ],
                ],
                [
                    'type' => 'text',
                    'name' => 'meta_description',
                    'label' => 'SEO Description',
                    'constraints' => [
                        [
                            'name' => 'bannedwords',
                            'customizable' => 1,
                        ],
                        [
                            'name' => 'maxlength',
                            'value' => '255',
                            'customizable' => 1,
                        ],
                    ],
                ],
                [
                    'type' => 'timezone',
                    'name' => 'timezone',
                    'label' => 'Timezone',
                    'constraints' => [
                        ['name' => 'required'],
                    ],
                ],
            ]);

            $reviewFields = [
                [
                    'type' => 'text',
                    'name' => 'title',
                    'label' => 'Title',
                    'constraints' => [
                        ['name' => 'required'],
                        [
                            'name' => 'bannedwords',
                            'customizable' => 1,
                        ],
                        [
                            'name' => 'maxlength',
                            'value' => '150',
                            'customizable' => 1,
                        ],
                    ],
                ],
                [
                    'type' => 'textarea',
                    'name' => 'description',
                    'label' => 'Review',
                    'constraints' => [
                        ['name' => 'required'],
                        [
                            'name' => 'bannedwords',
                            'customizable' => 1,
                        ],
                        [
                            'name' => 'maxlength',
                            'value' => '500',
                            'customizable' => 1,
                        ],
                    ],
                ],
            ];

            $messageFields = [
                [
                    'type' => 'text',
                    'name' => 'title',
                    'label' => 'Subject',
                    'constraints' => [
                        ['name' => 'required'],
                        [
                            'name' => 'bannedwords',
                            'customizable' => 1
                        ],
                        [
                            'name' => 'maxlength',
                            'value' => '150',
                            'customizable' => 1,
                        ],
                    ],
                ],
                [
                    'type' => 'textarea',
                    'name' => 'description',
                    'label' => 'Message',
                    'constraints' => [
                        ['name' => 'required'],
                        [
                            'name' => 'bannedwords',
                            'customizable' => 1
                        ],
                        [
                            'name' => 'maxlength',
                            'value' => '500',
                            'customizable' => 1,
                        ],
                    ],
                ],
            ];

            $this->insertFields($fields, 1);
            $this->insertFields($reviewFields, 2);
            $this->insertFields($messageFields, 3);            
        }

        return $result;
    }

    private function insertFields(array $fields, $groupId)
    {
        foreach ($fields as $field) {
            $item = new \App\Models\ListingField();
            $item->listingfieldgroup_id = $groupId;
            $item->submittable = 1;
            $item->updatable = 1;
            $item->customizable = $field['customizable'] ?? null;
            $item->removable = null;
            $item->sluggable = $field['sluggable'] ?? null;
            $item->value = $field['value'] ?? null;
            $item->upload_id = $field['upload_id'] ?? null;
            $item->search_type = 'eq';
            $item->type = $field['type'];
            $item->name = $field['name'];
            $item->value = $field['value'] ?? null;
            $item->setTranslation('label', $field['label'], 'en');
            $item->type_id = $this->get($this->getPrimaryKey());
            $item->schema_itemprop = $field['schema_itemprop'] ?? null;
            $item->save();

            if (isset($field['constraints'])) {
                foreach ($field['constraints'] as $constraint) {
                    $model = new \App\Models\ListingFieldConstraint($constraint);
                    $model->customizable = $constraint['customizable'] ?? null;
                    $model->listingfield_id = $item->get($item->getPrimaryKey());
                    $model->save();
                }
            }

            if (isset($field['options'])) {
                foreach ($field['options'] as $option) {
                    $model = new \App\Models\ListingFieldOption();
                    $model->name = $option['name'];
                    $model->setTranslation('value', $option['value'], 'en');
                    $model->listingfield_id = $item->get($item->getPrimaryKey());
                    $model->save();
                }
            }
        }

        return true;
    }

}
