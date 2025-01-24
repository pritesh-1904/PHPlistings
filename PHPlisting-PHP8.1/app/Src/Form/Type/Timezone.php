<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Timezone
    extends Select
{

    public $defaultConstraints = 'timezone';
    public $value = '+0000';
    public $timezones = [
        '-1200' => 'UTC-12',
        '-1100' => 'UTC-11',
        '-1000' => 'UTC-10',
        '-0930' => 'UTC-9:30',
        '-0900' => 'UTC-9',
        '-0800' => 'UTC-8',
        '-0700' => 'UTC-7',
        '-0600' => 'UTC-6',
        '-0500' => 'UTC-5',
        '-0400' => 'UTC-4',
        '-0330' => 'UTC-3:30',
        '-0300' => 'UTC-3',
        '-0200' => 'UTC-2',
        '-0100' => 'UTC-1',
        '+0000' => 'UTC',
        '+0100' => 'UTC+1',
        '+0200' => 'UTC+2',
        '+0300' => 'UTC+3',
        '+0330' => 'UTC+3:30',
        '+0400' => 'UTC+4',
        '+0430' => 'UTC+4:30',
        '+0500' => 'UTC+5',
        '+0530' => 'UTC+5:30',
        '+0545' => 'UTC+5:45',
        '+0600' => 'UTC+6',
        '+0630' => 'UTC+6:30',
        '+0700' => 'UTC+7',
        '+0800' => 'UTC+8',
        '+0845' => 'UTC+8:45',
        '+0900' => 'UTC+9',
        '+0930' => 'UTC+9:30',
        '+1000' => 'UTC+10',
        '+1030' => 'UTC+10:30',
        '+1100' => 'UTC+11',
        '+1200' => 'UTC+12',
        '+1245' => 'UTC+12:45',
        '+1300' => 'UTC+13',
        '+1400' => 'UTC+14',
    ];


    public function getOutputableValue($schema = false)
    {
        return $this->timezones[$this->getValue()];
    }

    public function exportValue()
    {
        return d($this->getValue());
    }

    public function importValue($value, $fieldModel, $locale)
    {
        if ('' != $value && false === array_key_exists($value, $this->timezones)) {
            return false;
        }

        return $value;
    }

    public function render()
    {
        return view('form/field/select', $this);
    }

    public function getOptions()
    {
        return $this->timezones;
    }

    public function setOptions(array $options)
    {
        return $this;
    }

}
