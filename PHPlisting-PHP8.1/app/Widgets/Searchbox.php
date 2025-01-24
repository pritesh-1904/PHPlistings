<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2022 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Searchbox
    extends \App\Src\Widget\BaseWidget
{

    protected $type = null;
    protected $translatable = [
        'heading',
        'description',
    ];

    public function isMultiInstance()
    {
        return true;
    }

    public function render()
    {        
        if (null === $this->getType()) {
            return null;
        }

        $widgets = $this->getWidgetizer()->all();

        foreach ($widgets as $widget) {
            if ($widget->slug == 'searchbox' && $widget->getWidgetObject()->isRendered()) {
                return null;
            }
        }

        $this->rendered = true;

        return view('widgets/searchbox', [
            'settings' => $this->getSettings(),
            'data' => $this->getData(),
            'form' => $this->getSearchForm(),
            'type' => $this->getType(),
            'types' => $this->getAllTypes(),
        ]);
    }

    public function getDefaultSettings()
    {
        return collect([
            'slider' => bin2hex(random_bytes(16)),
            'slider_speed' => '2000',
            'slider_autoplay_delay' => '10000',
            'types' => 1,
            'heading' => '{"en": "Find Local Businesses Near You!"}',
            'description' => '{"en": "Local events, cafes, things to do and more."}',
            'default_type_id' => 0,
            'hide_empty' => null,
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('slider', 'dropzone', ['label' => __('widget.searchbox.form.label.slider'), 'upload_id' => '6'])
            ->add('slider_speed', 'number', ['label' => __('widget.searchbox.form.label.slider_transition'), 'constraints' => 'required'])
            ->add('slider_autoplay_delay', 'number', ['label' => __('widget.searchbox.form.label.slider_autoplay_delay'), 'constraints' => 'required'])
            ->add('types', 'toggle', ['label' => __('widget.searchbox.form.label.types')])
            ->add('heading', 'translatable', ['label' => __('widget.searchbox.form.label.heading')])
            ->add('description', 'translatable', ['label' => __('widget.searchbox.form.label.description')])
            ->add('default_type_id', 'select', ['label' => __('widget.searchbox.form.label.type'), 'options' => [0 => __('widget.searchbox.form.label.type.auto')] + \App\Models\Type::whereNull('deleted')->get()->pluck('name_plural', 'id')->all(), 'constraints' => 'required|number'])
            ->add('hide_empty', 'toggle', ['label' => __('widget.searchbox.form.label.hide_empty')]);
    }

    public function getSearchForm()
    {
        $form = form()
            ->setMethod('get')
            ->add('keyword', 'text', ['placeholder' => __('listing.search.form.label.keyword')]);

        if ('Event' == $this->getType()->type) {
            $form->add('dates', 'dates', ['placeholder' => __('listing.search.form.label.dates')]);
        }

        if (null !== $this->getType()->localizable) {
            //$form->add('location_id', 'select', ['options' => ['' => __('listing.search.form.label.location')] + (new \App\Models\Location)->getDropdownTree(1)]);
            $form->add('location_id', 'location', ['placeholder' => __('listing.search.form.label.all_locations'), 'theme' => 'custom']);
        }

        $form->add('category_id', 'select', ['options' => ['' => __('listing.search.form.label.all_categories')] + (new \App\Models\Category)->getDropdownTree($this->getType()->id, null, 1, true, (null !== $this->getSettings()->get('hide_empty') ? true : false))]);

        return $form;
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

    public function getAllTypes()
    {
        $query = \App\Models\Type::whereNull('deleted')->orderBy('weight');
        
        if (false === auth()->check('admin_login')) {
            $query->whereNotNull('active');
        }
        
        return $query->get();
    }

}
