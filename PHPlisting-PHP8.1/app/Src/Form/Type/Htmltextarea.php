<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Htmltextarea
    extends Type
{

    public function __construct($name, array $options = [], \App\Src\Form\Builder $form = null)
    {
        if (false === isset($options['config']) || (false !== isset($options['config']) && 'advanced' != $options['config'])) {
            $this->defaultConstraints = 'htmlmaxtags:a,0';
        }

        parent::__construct($name, $options);
    }

    public function getOutputableValue($schema = false)
    {
        return $this->addSchema(
            purify(d($this->getValue())),
            $this->getItemProperty(),
            \strip_tags(d($this->getValue())),
            $schema
        );
    }

    public function importValue($value, $fieldModel, $locale)
    {
        return $this->sanitize($value);
    }

    public function render()
    {
        return view('form/field/htmltextarea', $this);
    }

    public function sanitize($value)
    {
        if ('advanced' != $this->get('config')) {
            $value = \strip_tags($value, '<h1><h2><h3><h4><h5><h6><strong><b><i><em><ul><ol><li><p><code><pre><span><br><a><blockquote>');
        }

        return e($value, true);
    }

}
