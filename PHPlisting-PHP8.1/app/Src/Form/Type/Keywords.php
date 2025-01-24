<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Keywords
    extends Type
{

    public function getOutputableValue($schema = false)
    {
        return str_replace(',', ', ', $this->getValue());
    }

    public function render()
    {
        return view('form/field/keywords', $this);
    }

}
