<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Youtube
    extends Type
{

    public $defaultConstraints = 'youtube';

    public function getOutputableValue($schema = false)
    {
        if ('' != $this->getValue()) {
            $regex = '/(?im)\b(?:https?:\/\/)?(?:w{3}\.)?youtu(?:be)?\.(?:com|be)\/(?:(?:\??v=?i?=?\/?)|watch\?vi?=|watch\?.*?&v=|embed\/|)([A-Z0-9_-]{11})\S*(?=\s|$)/';

            if (false !== preg_match_all($regex, $this->getValue(), $matches, PREG_SET_ORDER)) {
                if (false !== isset($matches[0]) && false !== isset($matches[0][1])) {
                    return view('form/field/outputable/youtube', ['value' => $matches[0][1]]);
                }
            }
        }
    }

    public function importValue($value, $fieldModel, $locale)
    {
        if ('' == $value) {
            return '';
        }

        $regex = '/(?im)\b(?:https?:\/\/)?(?:w{3}\.)?youtu(?:be)?\.(?:com|be)\/(?:(?:\??v=?i?=?\/?)|watch\?vi?=|watch\?.*?&v=|embed\/|)([A-Z0-9_-]{11})\S*(?=\s|$)/';

        \preg_match_all($regex, $value, $matches, PREG_SET_ORDER);

        if (false === isset($matches[0][1])) {
            return false;
        }

        return $this->sanitize($value);
    }

    public function render()
    {
        return view('form/field/text', $this);
    }

}
