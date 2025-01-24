<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2022 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Map
    extends \App\Src\Widget\BaseWidget
{

    protected $type = null;

    public function isMultiInstance()
    {
        return true;
    }

    public function render()
    {        
        if (null === $this->getType()) {
            return null;
        }

        $this->rendered = true;

        return view('widgets/map', [
            'settings' => $this->getSettings(),
            'data' => $this->getData(),
            'type' => $this->getType(),
        ]);
    }

    public function getDefaultSettings()
    {
        return collect([
            'latitude' => config()->map->latitude,
            'longitude' => config()->map->longitude,
            'zoom' => config()->map->zoom,
            'default_type_id' => 0,
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('mappicker', 'mappicker', ['label' => __('widget.map.form.label.mappicker')])
            ->add('latitude', 'number', ['label' => __('widget.map.form.label.latitude'), 'constraints' => 'required'])
            ->add('longitude', 'number', ['label' => __('widget.map.form.label.longitude'), 'constraints' => 'required'])
            ->add('zoom', 'number', ['label' => __('widget.map.form.label.zoom'), 'constraints' => 'required|min:0|max:20'])
            ->add('default_type_id', 'select', ['label' => __('widget.map.form.label.type'), 'options' => [0 => __('widget.map.form.label.type.auto')] + \App\Models\Type::whereNull('deleted')->get()->pluck('name_plural', 'id')->all(), 'constraints' => 'required']);
    }

    public function getType()
    {
        if (!$this->type instanceof \App\Models\Type) {
            if ($this->getSettings()->default_type_id == 0 && null !== $this->getData()->get('type')) {
                $this->type = $this->getData()->type;
            }

            if ($this->getSettings()->default_type_id > 0) {
                $query = \App\Models\Type::where('id', $this->getSettings()->default_type_id)->whereNull('deleted');

                if (false === auth()->check('admin_login')) {
                    $query->whereNotNull('active');
                }

                if (null !== $type = $query->first()) {
                    $this->type = $type;
                }
            }
        }

        return $this->type;
    }

}
