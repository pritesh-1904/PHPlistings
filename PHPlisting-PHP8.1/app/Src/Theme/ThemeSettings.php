<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Theme;

abstract class ThemeSettings
{

    private $settings = null;

    public function setSettings(array $settings)
    {
        $this->settings = $this->getDefaultSettings()
            ->merge(collect($settings))
            ->all();

        return $this;
    }

    public function getSettings()
    {
        return $this->settings;
    }

    public function getDefaultSettings()
    {
        return collect();
    }

    public function getConfigurationForm(\App\Src\Form\Builder $form)
    {
        return $form;
    }

}
