<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Gateway;

abstract class BaseGateway
{

    protected $gateway;
    protected $model;

    public function __construct(\App\Models\Gateway $model)
    {
        $this->setModel($model);
    }

    public function setModel(\App\Models\Gateway $model)
    {
        $this->model = $model;

        return $this;
    }
    
    public function getModel()
    {
        return $this->model;
    }
    
    public function getSettings()
    {
        return collect($this->model->getSettings());
    }

    public function purchase(\App\Models\Transaction $transaction, \App\Src\Support\Collection $input = null)
    {
        throw new \App\Src\Gateway\InvalidRequestException('Method not supported.');
    }

    public function complete(\App\Models\Transaction $transaction)
    {
        throw new \App\Src\Gateway\InvalidRequestException('Method not supported.');
    }

    public function notification()
    {
        throw new \App\Src\Gateway\InvalidRequestException('Method not supported.');
    }

    public function render()
    {
        throw new \App\Src\Gateway\InvalidRequestException('Method not supported.');
    }

    public function getRedirectForm(\App\Src\Form\Builder $form, $response)
    {
        return $form;
    }

    public function getConfigurationForm(\App\Src\Form\Builder $form)
    {
        return $form;
    }

    public function getForm(\App\Src\Form\Builder $form)
    {
        return $form;
    }

    public function formatPrice($value)
    {
        return number_format($value, 2, '.', '');
    }

}
