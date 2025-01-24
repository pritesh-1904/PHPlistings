<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Gateways;

class Offline
    extends \App\Src\Gateway\BaseGateway
{

    public function getConfigurationForm(\App\Src\Form\Builder $form)
    {
        return $form->add('message', 'htmltextarea', ['label' => __('admin.gateways.offline.label.message')]);
    }

    public function render()
    {
        return view('flash/primary', [
            'message' => d($this->getSettings()->message),
        ]);
    }

}
