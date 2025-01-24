<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Accountheader
    extends \App\Src\Widget\BaseWidget
{

    public function render()
    {
        $this->rendered = true;

        if (auth()->check()) {
            return view('widgets/account-header', [
                'settings' => $this->getSettings(),
                'data' => $this->getData(),
            ]);
        } else {
            return null;
        }
    }

    public function getDefaultSettings()
    {
        return collect([
            'image' => bin2hex(random_bytes(16)),
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('image', 'dropzone', ['label' => __('widget.accountheader.form.label.image'), 'upload_id' => '5']);
    }

}
