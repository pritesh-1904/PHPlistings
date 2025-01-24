<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Icon
    extends Type
{

    public function getOutputableValue($schema = false)
    {
        return view('form/field/outputable/icon', ['value' => $this->getValue()]);
    }

    public function render()
    {
        return view('form/field/icon', $this);
    }

}
