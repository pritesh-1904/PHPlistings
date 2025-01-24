<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Ro
    extends Type
{

    public function getOutputableValue($schema = false)
    {
        return null;
    }

    public function render()
    {
        return view('form/field/readonly', $this);
    }

}
