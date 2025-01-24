<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Token
    extends Type
{

    public function isHidden()
    {
        return true;
    }

    public function setValue($value)
    {
        if (!session()->has('__token')) {
            $token = bin2hex(random_bytes(16));
            session()->put('__token', $token);
        } else {
            $token = session()->get('__token');
        }

        $this->value = $token;

        return $this;
    }

    public function getConstraints()
    {
        $token = $this->value;

        return [function ($value, $context) use ($token) {
            if ($token !== $value) {
                throw new \App\Src\Validation\ValidatorException(__('form.validation.token'));
            }
        }];

    }

    public function getOutputableValue($schema = false)
    {
        return null;
    }

    public function render()
    {
        return view('form/field/hidden', $this);
    }

}
