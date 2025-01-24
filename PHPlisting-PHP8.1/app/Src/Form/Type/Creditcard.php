<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Creditcard
    extends Type
{

    public $defaultConstraints = 'creditcard';

    public function render()
    {
        return view('form/field/creditcard', $this);
    }

}
