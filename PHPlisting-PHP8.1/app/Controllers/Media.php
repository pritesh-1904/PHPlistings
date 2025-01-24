<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2022 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers;

class Media
    extends \App\Src\Mvc\BaseController
{

    public function actionIndex($params)
    {
        if (isset($params['id'])) {
            if (isset($params['type']) && !in_array($params['type'], ['small', 'medium', 'large'])) {
                throw new \App\Src\Http\NotFoundHttpException();
            }

            $file = \App\Models\File::query()
                ->where('id', $params['id'])
                ->first();

            if (null !== $file) {
                $file = (isset($params['type'])) ? $file->{$params['type']}() : $file;
                if ($file->name . '.' . $file->extension == $params['name']) {
                    try {
                        return fileResponse($file->getPath())
                            ->withEtag(md5($file->getUrl()))
                            ->withHeaders([
                                'Cache-Control' => 'max-age=31536000',
                            ]);
                    } catch (\App\Src\Http\File\FileNotFoundException $e) {
                        throw new \App\Src\Http\NotFoundHttpException();
                    }
                }
            }
        }

        throw new \App\Src\Http\NotFoundHttpException();
    }

}
