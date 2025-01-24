<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\DataTransformer;

class LocalizedStringToFloat
    implements DataTransformerInterface
{

    protected $thousandsSeparator;
    protected $decimalSeparator;
    protected $decimalPlaces;

    public function __construct($thousandsSeparator = '', $decimalSeparator = '.', $decimalPlaces = null)
    {
        $this->thousandsSeparator = $thousandsSeparator;
        $this->decimalSeparator = $decimalSeparator;
        $this->decimalPlaces = $decimalPlaces;

        if ($thousandsSeparator == $decimalSeparator) {
            throw new FailedTransformationException('Thousands and decimal separators must use unique values');
        }
    }

    public function transform($value)
    {
        if ('' === $value || null === $value) {
            return $value;
        }
        
        if (!is_string($value)) {
            throw new FailedTransformationException('Expected string');
        }

        $value = str_replace($this->thousandsSeparator, '', $value);

        if ('.' !== $this->decimalSeparator) {
            $value = str_replace($this->decimalSeparator, '.', $value);
        }

        if (null !== $this->decimalPlaces) {
            $value = number_format($value, $this->decimalPlaces, '.', '');
        }

        return $value;
    }

    public function reverseTransform($value)
    {
        if ('' === $value || null === $value) {
            return $value;
        }

        if (!is_numeric($value)) {
            throw new FailedTransformationException('Expected numeric value');
        }

        if (null === $this->decimalPlaces) {
            $this->decimalPlaces = 0;
            $fragments = explode('.', $value);
            if (count($fragments) == 2) {
                $this->decimalPlaces = strlen($fragments[1]);
            }
        }

        return number_format($value, $this->decimalPlaces, $this->decimalSeparator, $this->thousandsSeparator);
    }

}
