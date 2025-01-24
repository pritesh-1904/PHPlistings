<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Translatable
    extends Type
{

    private function getTransformer()
    {
        return new \App\Src\DataTransformer\ArrayToJson();
    }

    public function transform($value)
    {
        return $this->getTransformer()->transform($value);
    }

    public function reverseTransform($value)
    {
        return $this->getTransformer()->reverseTransform($value);
    }

    public function forceTransformOnError()
    {
        return true;
    }
    
    public function sanitize($value)
    {
        return parent::sanitize(array_intersect_key((array) $value, array_flip(locale()->getSupported())));
    }

    public function render()
    {
        return view('form/field/translatable', $this);
    }

}
