<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Phone
    extends Type
{

    public $defaultConstraints = 'phone';

    public function importValue($value, $fieldModel, $locale)
    {
        if ('' != $value && false === preg_match('/^[0-9-+ ()\/]+$/', $value)) {
            return false;
        }

        return $this->sanitize($value);
    }

    public function render()
    {
        return view('form/field/text', $this);
    }

}
