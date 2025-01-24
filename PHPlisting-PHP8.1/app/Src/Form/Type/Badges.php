<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2024 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Badges
    extends Type
{

    public $defaultConstraints = 'array';

    public function getOptions()
    {
        $query = \App\Models\Badge::query();
        
        if (null !== $this->get('type_id') && null !== $type = \App\Models\Type::whereNull('deleted')->where('id', $this->get('type_id'))->first()) {
            $query = $type->badges();
        }

        if (null !== $this->get('public')) {
            $query->whereNotNull('public');
        }

        return $query->get()->pluck(function ($badge) { return $badge; }, 'id')->all();
    }
    
    public function render()
    {
        return view('form/field/badges', $this);
    }

}
