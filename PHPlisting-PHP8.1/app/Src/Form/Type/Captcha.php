<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Captcha
    extends Type
{

    public function __construct($name, array $options = [], \App\Src\Form\Builder $form = null)
    {
        parent::__construct($name, $options);

        layout()->addJs('<script src="https://www.google.com/recaptcha/api.js"></script>');
    }

    public function setValue($value)
    {
        $this->value = '<div class="g-recaptcha" data-sitekey="' . e(config()->general->captcha_site_key) . '"></div>';

        return $this;
    }

    public function resetValue()
    {
        return null;
    }

    public function getConstraints()
    {
        return [function($value, $context = null) {
            if (isset($context['g-recaptcha-response'])) {
                if (false !== $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . config()->general->captcha_secret_key . '&response=' . $context['g-recaptcha-response'])) {
                    $response = json_decode($response, true);
                    if ($response["success"] === true) {
                        return;
                    }
                }
            }

            throw new \App\Src\Validation\ValidatorException(__('form.validation.captcha'));
        }];
    }

    public function getOutputableValue($schema = false)
    {
        return null;
    }

    public function exportValue()
    {
        return '';
    }

    public function importValue($value, $fieldModel, $locale)
    {
        return '';
    }

    public function render()
    {
        return view('form/field/custom', $this);
    }

}
