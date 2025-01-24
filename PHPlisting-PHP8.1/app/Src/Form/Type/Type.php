<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Type
    extends \App\Src\Support\Collection
    implements TypeInterface
{

    protected $form;
    public $errors = [];
    public $defaultConstraints = null;

    public function __construct($name, array $options = [], \App\Src\Form\Builder $form = null)
    {
        $this->collect($options);

        $this->form = $form;
        $this->name = $name;
        $this->attributes = new \App\Src\Html\Attributes($options['attributes'] ?? []);
        $this->attributes->id = $this->attributes->id ?? $name;
        $this->setValue($this->value ?? null);

        if (null !== $this->defaultConstraints) {
            $this->addConstraintsIfNotExist($this->defaultConstraints);
        }
    }

    public function getForm()
    {
        return $this->form;
    }

    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    public function getValue()
    {
        return $this->value ?? null;
    }

    public function resetValue()
    {
        $this->value = null;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getLabel()
    {
        return $this->label ?? null;
    }

    public function hasErrors()
    {
        return count($this->errors) > 0;
    }

    public function setError($error)
    {
        $this->errors[] = $error;

        return $this;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getOptions()
    {
        return $this->options ?? [];
    }

    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    public function getConstraints()
    {
        return $this->constraints ?? [];
    }

    public function addConstraints($string, $overwrite = true) {
        $segments = explode('|', $string);

        foreach ($segments as $constraint) {
            $this->addConstraint($constraint, $overwrite);
        }

        return $this;
    }

    public function addConstraintsIfNotExist($string) {
        return $this->addConstraints($string, false);
    }

    public function addConstraint($string, $overwrite = true) {

        $segments = explode(':', $string);

        if ($segments[0] != '') {
            $constraints = $this->get('constraints', []);

            $altered = false;

            $class = 'App\\Src\\Validation\\' . ucfirst(strtolower(trim($segments[0]))) . 'Validator';

            $parameters = explode(',', ($segments[1] ?? ''));

            $constraint = new $class(...$parameters);
                
            foreach ($constraints as $key => $value) {
                if ($class == get_class($value)) {
                    $altered = true;

                    if (false !== $overwrite) {
                        $constraints[$key] = $constraint;
                    }
                }
            }

            if (false === $altered) {
                array_push($constraints, $constraint);
            }

            $this->put('constraints', $constraints);
        }

        return $this;
    }

    public function removeConstraint($string)
    {
        $constraints = $this->get('constraints', []);

        $class = 'App\\Src\\Validation\\' . ucfirst(strtolower(trim($string))) . 'Validator';

        foreach ($constraints as $key => $value) {
            if (false !== get_class($value) && $class == get_class($value)) {
                unset($constraints[$key]);
            }
        }

        $this->put('constraints', $constraints);

        return $this;
    }

    public function getWeight()
    {
        return $this->weight ?? 0;
    }

    public function isAction()
    {
        return false;
    }

    public function isHidden()
    {
        return false;
    }

    public function isRequired()
    {
        return (false === ($this instanceof \App\Src\Form\Type\Dropzone) && false === ($this instanceof \App\Src\Form\Type\Hours) && in_array(new \App\Src\Validation\RequiredValidator, $this->getConstraints()))
            || in_array(new \App\Src\Validation\TransrequiredValidator, $this->getConstraints())
            || in_array(new \App\Src\Validation\FilerequiredValidator, $this->getConstraints());
    }

    public function isSeparator()
    {
        return false;
    }

    public function sanitize($value)
    {
        if (null === $value) {
            return null;
        }
        
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = self::sanitize($v);
            }
        } else if (is_string($value)) {
            $value = e($value);
        }

        return $value;
    }

    public function transform($value)
    {
        return $value;
    }

    public function reverseTransform($value)
    {
        return $value;
    }

    public function forceTransformOnError()
    {
        return false;
    }

    public function getOutputableValue($schema = false)
    {
        return $this->addSchema($this->getValue(), $this->getItemProperty(), null, $schema);
    }

    public function getOutputableValueWithSchema()
    {
        return $this->getOutputableValue(true);
    }

    public function exportValue()
    {
        return d($this->getValue());
    }

    public function importValue($value, $fieldModel, $locale)
    {
        return $this->sanitize($value);
    }

    public function getItemProperty()
    {
        return $this->get('itemprop');
    }

    public function getIcon()
    {
        return $this->get('icon');
    }

    protected function addSchema($value, $itemprop, $content = null, $enabled = true) {
        if (false !== $enabled) {
            return (null !== $itemprop && '' !== $itemprop ? '<span itemprop="' . $itemprop . '"' . (null !== $content ? ' content="' . $content . '"' : '') . '>' . $value . '</span>' : $value);
        }

        return $value;
    }

    public function render()
    {
        return;
    }

}
