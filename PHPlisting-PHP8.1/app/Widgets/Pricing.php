<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2022 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Pricing
    extends \App\Src\Widget\BaseWidget
{

    protected $type = null;
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
        if (null === $this->getType()) {
            return null;
        }

        $products = $this->getProducts();

        if ($products->count() == 0) {
            return null;
        }

        $this->rendered = true;

        return view('widgets/pricing', [
            'settings' => $this->getSettings(),
            'data' => $this->getData(),
            'type' => $this->getType(),
            'products' => $products,
        ]);
    }

    public function getDefaultSettings()
    {
        return collect([
            'colorscheme' => 'bg-white',
            'caption' => '{"en": "Compare features"}',
            'heading' => '{"en": "Plans & Pricing"}',
            'options' => 1,
            'fields' => null,
            'default_type_id' => 0,
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('colorscheme', 'select', ['label' => __('widget.pricing.form.label.colorscheme'), 'options' => [
                'bg-white' => __('widget.pricing.form.label.white'),
                'bg-light' => __('widget.pricing.form.label.light'),
             ]])
            ->add('caption', 'translatable', ['label' => __('widget.pricing.form.label.caption')])
            ->add('heading', 'translatable', ['label' => __('widget.pricing.form.label.heading')])
            ->add('options', 'toggle', ['label' => __('widget.pricing.form.label.options')])
            ->add('fields', 'toggle', ['label' => __('widget.pricing.form.label.fields')])
            ->add('default_type_id', 'select', ['label' => __('widget.pricing.form.label.type'), 'options' => [0 => __('widget.pricing.form.label.type.auto')] + \App\Models\Type::whereNull('deleted')->get()->pluck('name_plural', 'id')->all(), 'constraints' => 'required|number']);
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

    public function getProducts()
    {
        $query = $this->getType()
            ->products()
            ->whereNull('hidden')
            ->with('pricings', function ($query) {
                return $query
                    ->whereNull('hidden')
                    ->orderBy('weight');
            });

        if (null !== $this->getSettings()->fields) {
            $query
                ->with('fields', function ($query) {
                    return $query
                        ->where('listingfieldgroup_id', 1)
                        ->orderBy('weight');
                });
        }

        return $query
            ->orderBy('weight')
            ->get();
    }

}
