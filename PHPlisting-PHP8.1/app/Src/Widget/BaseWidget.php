<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Widget;

abstract class BaseWidget
{

    private $widgetizer = null;
    private $settings = null;
    private $id;
    private $data;

    protected $compiled = false;
    protected $rendered = false;

    public function isMultiInstance()
    {
        return false;
    }

    public function setWidgetizer(\App\Repositories\Widgetizer $widgetizer)
    {
        $this->widgetizer = $widgetizer;

        return $this;
    }

    public function isWidgetized()
    {
        return null !== $this->widgetizer;
    }

    public function getWidgetizer()
    {
        return $this->widgetizer;
    }

    public function isCompiled()
    {
        return false !== $this->compiled;
    }

    public function isRendered()
    {
        return false !== $this->rendered;
    }

    public function compile()
    {
        $this->compiled = true;

        return true;
    }

    public function render()
    {
        $this->rendered = true;

        return '';
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setSettings(array $settings)
    {
        $settings = $this->getDefaultSettings()
            ->merge(collect($settings))
            ->all();

        $this->settings = (new WidgetSettings($settings))
            ->setTranslatable($this->translatable ?? null);

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

    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getForm()
    {
        return;
    }

}
