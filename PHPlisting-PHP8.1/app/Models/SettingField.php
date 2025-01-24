<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class SettingField
    extends \App\Src\Orm\Model
{

    protected $table = 'settingfields';

    public function getLabel()
    {
        return __('admin.settings.form.label.' . $this->label);
    }

    public function getConstraints()
    {
        return $this->constraints;
    }

    public function getOptions()
    {
        if ($this->options === null || $this->options === '') {
            return [];
        }

        if ($this->options_type == 'eval') {
            return eval(d($this->options));
        }

        $options = array_map('trim', (explode("\n", $this->options)));

        $array = [];

        foreach ($options as $key => $option) {
            if(false !== strstr($option, '|') && count($segments = explode('|', $option)) == 2) {
                $array[$segments[0]] = __('admin.settings.form.option.' . $this->name . '.' . $segments[1]);
            } else {
                $array[$key] = __('admin.settings.form.option.' . $this->name . '.' . $option);
            }
        }

        return $array;
    }

    public function getDefaultValue()
    {
        return $this->value;
    }

    public function getSluggable()
    {
        return;
    }

    public function getPlaceholder()
    {
        return;
    }

    public function getDescription()
    {
        return ('' != $this->get('description', '')) ? __('admin.settings.form.description.' . $this->name . '.' . $this->description) : null;
    }

    public function getItemProperty()
    {
        return null;
    }

    public function getIcon()
    {
        return null;
    }

}
