<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Email
    extends Type
{

    public $defaultConstraints = 'email|bannedemaildomain';

    public function importValue($value, $fieldModel, $locale)
    {
        if ('' != $value && false === filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return $this->sanitize($value);
    }

    public function render()
    {
        return view('form/field/text', $this);
    }

}
