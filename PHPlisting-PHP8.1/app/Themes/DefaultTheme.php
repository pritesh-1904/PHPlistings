<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2022 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Themes;

class DefaultTheme
    extends \App\Src\Theme\BaseTheme
{

    private $fonts = [
        'Nunito Sans' => 'Nunito Sans',
        'Open Sans' => 'Open Sans',
        'Inter' => 'Inter',
        'Ubuntu' => 'Ubuntu',
        'Quicksand' => 'Quicksand',
    ];

    private $fontSizes = [
        '16' => '16',
        '17' => '17',
        '18' => '18',
        '19' => '19',
        '20' => '20',
    ];

    private $fontWeights = [
        '300' => '300',
        '400' => '400',
        '600' => '600',
        '700' => '700',
        '800' => '800',
        '900' => '900',
    ];

    public function getDefaultSettings()
    {
        return collect([
            'bodyFontFamily' => 'Quicksand',
            'bodyFontSize' => '18',
            'bodyFontWeight' => '400',
            'bodyFontColor' => 'rgb(50, 50, 50)'
        ]);
    }

    public function getConfigurationForm(\App\Src\Form\Builder $form)
    {
        return $form
            ->add('body', 'separator', ['label' => __('theme.default.separator.body')])
            ->add('bodyFontFamily', 'select', ['label' => __('theme.default.label.bodyFontFamily'), 'options' => $this->fonts])
            ->add('bodyFontSize', 'select', ['label' => __('theme.default.label.bodyFontSize'), 'options' => $this->fontSizes])
            ->add('bodyFontWeight', 'select', ['label' => __('theme.default.label.bodyFontWeight'), 'options' => $this->fontWeights])
            ->add('bodyFontColor', 'color', ['label' => __('theme.default.label.bodyFontColor'), 'type' => 'rgb']);
    }

}
