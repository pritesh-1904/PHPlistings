<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2022 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Searchbar
    extends \App\Src\Widget\BaseWidget
{

    protected $type = null;
    protected $translatable = [
        'heading',
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
            if ($widget->slug == 'searchbar' && $widget->getWidgetObject()->isRendered()) {
                return null;
            }
        }

        $this->rendered = true;

        return view('widgets/searchbar', [
            'settings' => $this->getSettings(),
            'data' => $this->getData(),
            'form' => $this->getSearchForm(),
            'type' => $this->getType(),
        ]);
    }

    public function getDefaultSettings()
    {
        return collect([
            'colorscheme' => 'bg-secondary',
            'default_type_id' => 0,
            'hide_empty' => null,
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('colorscheme', 'select', ['label' => __('widget.searchbar.form.label.colorscheme'), 'options' => [
                'bg-light' => __('widget.searchbar.form.label.light'),
                'bg-secondary' => __('widget.searchbar.form.label.grey'),
                'bg-success' => __('widget.searchbar.form.label.green'),
                'bg-primary' => __('widget.searchbar.form.label.blue'),
                'bg-warning' => __('widget.searchbar.form.label.yellow'),
                'bg-danger' => __('widget.searchbar.form.label.red'),
             ]])
            ->add('default_type_id', 'select', ['label' => __('widget.searchbar.form.label.type'), 'options' => [0 => __('widget.searchbar.form.label.type.auto')] + \App\Models\Type::whereNull('deleted')->get()->pluck('name_plural', 'id')->all(), 'constraints' => 'required|number'])
            ->add('hide_empty', 'toggle', ['label' => __('widget.searchbar.form.label.hide_empty')]);
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
//            $form->add('location_id', 'select', ['options' => ['' => __('listing.search.form.label.location')] + (new \App\Models\Location)->getDropdownTree(2)]);
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

}
