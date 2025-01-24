<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */
namespace App\Src\Form\Type;

class Locationmappicker
    extends Type
{

    public function getOutputableValue($schema = false)
    {
        return null;
    }

    public function exportValue()
    {
        if (is_numeric($this->getValue()) && null !== $location = \App\Models\Location::find($this->getValue())) {
            return $location->ancestorsAndSelfWithoutRoot()->get(['name'])->pluck('name')->implode('->');
        }

        return null;
    }

    public function importValue($value, $fieldModel, $locale)
    {
        $value = explode('->', $value);

        $current = (new \App\Models\Location)->getRoot();

        foreach ($value as $location) {
            $location = trim($this->sanitize($location));
            
            if ('' == $location) {
                return false;
            }

            $temp = \App\Models\Location::where('_parent_id', $current->id)
                ->where('name', 'like', '%"' . $locale . '":"' . $location . '"%')
                ->first();

            if (null === $temp) {
                $temp = new \App\Models\Location();
                $temp->appendTo($current);
                $temp->fill([
                    'active' => 1,
                    'featured' => null,
                    'slug' => slugify(d($location)),
                    'logo_id' => bin2hex(random_bytes(16)),
                    'header_id' => bin2hex(random_bytes(16)),
                    'latitude' => config()->map->latitude,
                    'longitude' => config()->map->longitude,
                    'zoom' => config()->map->zoom,
                ]);

                $temp->setTranslation('name', $location, config()->app->locale_fallback);

                if (config()->app->locale_fallback != $locale) {
                    $temp->setTranslation('name', $location, $locale);
                }

                $temp->save();
            }

            $current = $temp;
        }

        return $current->id;
    }

    public function render()
    {
        return view('form/field/locationmappicker', $this);
    }

}
