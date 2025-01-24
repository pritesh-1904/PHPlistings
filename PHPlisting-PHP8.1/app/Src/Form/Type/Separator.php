<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Separator
    extends Custom
{

    public function isSeparator()
    {
        return true;
    }

    public function getOutputableValue($schema = false)
    {
        return '';
    }

    public function exportValue()
    {
        return '';
    }

    public function importValue($value, $fieldModel, $locale)
    {
        return '';
    }

    public function render()
    {
        return view('form/field/separator', $this);
    }

}
