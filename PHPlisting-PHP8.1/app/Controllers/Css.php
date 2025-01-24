<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers;

class Css
    extends \App\Src\Mvc\BaseController
{

    public function actionIndex($params)
    {
        if (null !== $theme = \App\Models\Theme::where('slug', $params['slug'])->first()) {
            if (file_exists(config()->view->path . DS . $theme->get('slug') . DS . 'style.css.php')) {
                return response(minify(view('style', $theme->getThemeSettingsObject()->getSettings(), 'css.php')))
                    ->withHeaders([
                        'Content-type' => 'text/css',
                        'Cache-Control' => 'max-age=2592000'
                    ]);
            }
        }

        throw new \App\Src\Http\NotFoundHttpException();
    }

}
