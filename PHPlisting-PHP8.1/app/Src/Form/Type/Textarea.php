<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Textarea
    extends Type
{

    public function getOutputableValue($schema = false)
    {
        return $this->addSchema(
            \nl2br($this->getValue(), false),
            $this->getItemProperty(),
            null,
            $schema
        );
    }

    public function render()
    {
        return view('form/field/textarea', $this);
    }

}
