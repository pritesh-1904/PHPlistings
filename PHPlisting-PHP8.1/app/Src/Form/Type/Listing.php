<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Listing
    extends Type
{

    public $defaultConstraints = 'listing';

    public function render()
    {
        return view('form/field/listing', $this);
    }

}
