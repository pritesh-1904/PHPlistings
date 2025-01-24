<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Hours
    extends Type
{

    public $defaultConstraints = 'required';

    public function getOutputableValue($schema = false)
    {
        if ('' != $this->getValue()) {
            $hours = \App\Models\Hour::where('hash', $this->getValue())
                ->orderBy('dow')
                ->orderBy('start_time')
                ->get(['dow', 'start_time', 'end_time']);

            if ($hours->count() == 0) {
                return null;
            }
            
            return view('form/field/outputable/hours', ['value' => $hours, 'timezone' => (null !== $this->getForm() ? $this->getForm()->getTimezone() : config()->general->timezone)]);
        }
    }

    public function exportValue()
    {
        $fragments = [];
        
        if ('' != $this->getValue()) {
            $hours = \App\Models\Hour::where('hash', $this->getValue())
                ->orderBy('dow')
                ->orderBy('start_time')
                ->get(['dow', 'start_time', 'end_time']);

            foreach ($hours as $hour) {
                $fragments[] = $hour->dow . '-' . $hour->start_time . '-' . $hour->end_time;
            }
        }

        return implode(',', $fragments);
    }

    public function importValue($value, $fieldModel, $locale)
    {
        $hash = bin2hex(random_bytes(16));
        
        $value = array_map('trim', explode(',', $value));

        foreach ($value as $hours) {
            $elements = explode('-', $hours);

            if ($elements[0] > 0 && $elements[0] <= 7 && false !== isset($elements[1]) && false !== isset($elements[2])) {
                if (isset($elements[1]) && isset($elements[2]) && false !== \DateTime::createFromFormat('H:i:s', $elements[1]) && false !== \DateTime::createFromFormat('H:i:s', $elements[2])) {
                    if (strtotime($elements[1]) < strtotime($elements[2]) && strtotime($elements[1]) != strtotime($elements[2])) {
                        $model = new \App\Models\Hour();
                        $model->forceFill([
                            'hash' => $hash,
                            'dow' => $elements[0],
                            'start_time' => $elements[1],
                            'end_time' => $elements[2],
                        ]);
                        $model->save();
                    }
                }
            }
        }

        return $hash;
    }

    public function render()
    {
        return view('form/field/hours', $this);
    }

    public function setValue($value)
    {
        if ($value == '') {
            $this->value = bin2hex(random_bytes(16));
        } else {
            $this->value = $value;
        }
    }

}
