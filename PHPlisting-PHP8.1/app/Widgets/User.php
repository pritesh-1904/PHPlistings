<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2022 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class User
    extends \App\Src\Widget\BaseWidget
{
    protected $translatable = [
        'caption',
        'heading',
    ];

    public function isMultiInstance()
    {
        return true;
    }

    public function render()
    {
        $widgets = $this->getWidgetizer()->all();

        foreach ($widgets as $widget) {
            if ($widget->slug == 'user' && $widget->getWidgetObject()->isRendered()) {
                return null;
            }
        }

        if (null !== $this->getData()->get('listing') && $this->getData()->listing instanceof \App\Models\Listing) {
            $this->rendered = true;

            return view('widgets/user', [
                'settings' => $this->getSettings(),
                'data' => $this->getData(),
                'types' => $this->getAllTypesWithCount($this->getData()->get('listing')->user),
            ]);
        }
    }

    public function getDefaultSettings()
    {
        return collect([
            'colorscheme' => 'bg-white',
            'caption' => '{"en":"This listing is managed by"}',
            'heading' => '{"en":"Listing Owner"}',
            'fields' => 1,
            'types' => 1,
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('colorscheme', 'select', ['label' => __('widget.user.form.label.colorscheme'), 'options' => [
                'bg-white' => __('widget.user.form.label.white'),
                'bg-light' => __('widget.user.form.label.light'),
             ]])
            ->add('caption', 'translatable', ['label' => __('widget.user.form.label.caption')])
            ->add('heading', 'translatable', ['label' => __('widget.user.form.label.heading')])
            ->add('fields', 'toggle', ['label' => __('widget.user.form.label.fields')])
            ->add('types', 'toggle', ['label' => __('widget.user.form.label.types')]);
    }

    private function getAllTypes()
    {
        $query = \App\Models\Type::whereNull('deleted')->orderBy('weight');

        if (false === auth()->check('admin_login')) {
            $query->whereNotNull('active');
        }

        return $query->get();
    }

    public function getAllTypesWithCount(\App\Models\User $user)
    {
        $types = $this->getAllTypes();

        $listings = $user->listings()
            ->select('COUNT(*) as counter, type_id')
            ->whereNotNull('active')
            ->where('status', 'active')
            ->groupBy('type_id')
            ->get([1]);

        foreach ($types as $type) {
            $counter = 0;

            if (null !== $listing = $listings->where('type_id', $type->id)->first()) {
                $counter = $listing->counter;
            }
            
            $type->put('counter', $counter);
        }

        return $types;
    }

}
