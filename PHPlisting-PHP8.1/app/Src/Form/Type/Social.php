<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2022 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Social
    extends Url
{

    public function getOutputableValue($schema = false)
    {
        if (null !== $this->get('socialprofiletype_id')) {
            if (null !== $type = \App\Models\SocialProfileType::find($this->get('socialprofiletype_id'))) {
                return $this->addSchema(
                    view('form/field/outputable/social', ['value' => $this->getValue(), 'type' => $type]),
                    $this->getItemProperty(),
                    null,
                    $schema
                );
            }
        }
        
        return null;
    }

}
