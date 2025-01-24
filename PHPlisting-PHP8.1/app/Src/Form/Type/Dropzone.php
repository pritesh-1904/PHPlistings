<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Dropzone
    extends Type
{

    public $defaultConstraints = 'required';

    public function getOutputableValue($schema = false)
    {
        if ('' != $this->getValue()) {
            $uploads = \App\Models\File::where('document_id', $this->getValue())->get();

            return view('form/field/outputable/dropzone', ['value' => $uploads]);
        }
    }

    public function exportValue()
    {
        $urls = [];            

        if ('' != $this->getValue()) {
            $uploads = \App\Models\File::where('document_id', $this->getValue())->get();

            foreach ($uploads as $upload) {
                $urls[] = $upload->getUrl();
            }
        }

        return implode(',', $urls);
    }

    public function importValue($value, $fieldModel, $locale)
    {
        $path = ROOT_PATH_PROTECTED . DS . 'Imports' . DS . 'Temp' . DS . 'temp';
        
        $hash = bin2hex(random_bytes(16));
        
        $value = array_map('trim', explode(',', $value));

        if (null !== $type = \App\Models\UploadType::find($fieldModel->get('upload_id'))) {
            foreach ($value as $url) {
                if (false === filter_var($url, FILTER_VALIDATE_URL) || false === array_key_exists('scheme', parse_url($url))) {
                    continue;
                }

                $remotePath = parse_url($url, PHP_URL_PATH);
                $extension = pathinfo($remotePath, PATHINFO_EXTENSION);
                $filename = pathinfo(parse_url($remotePath, PHP_URL_PATH), PATHINFO_FILENAME);

                $data = file_get_contents($url);

                if (false !== $data) {
                    if (false !== file_put_contents($path, $data)) {
                        $file = new \App\Src\Http\File\File($path);

                        if (null !== $fileType = $type->fileTypes()
                            ->where('mime', $file->getMimeType())
                            ->first()) {

                            $model = \App\Models\File::create([
                                'name' => slugify($filename),
                                'extension' => slugify($extension),
                                'uploadtype_id' => $type->id,
                                'document_id' => $hash,
                                'mime' => $file->getMimeType(),
                                'size' => $file->getSize(),
                                'user_id' => 1,
                                'ip' => '127.0.0.1',
                                'version' => 1,
                            ]);

                            if (in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
                                foreach (['large', 'medium', 'small'] as $size) {
                                    $image = new \App\Src\Support\Image($file);
                                    $model->image_width = $image->getSourceWidth();
                                    $model->image_height = $image->getSourceHeight();
                                    $model->put($size . '_image_width', $type->get($size . '_image_width'));
                                    $model->put($size . '_image_height', $type->get($size . '_image_height'));

                                    if ($type->get($size . '_image_resize_type') == '1') {
                                        $model->put($size . '_image_height', round($type->get($size . '_image_width') / $type->cropbox_width * $type->cropbox_height));
                                        $image->crop($type->get($size . '_image_width'), round($type->get($size . '_image_width') / $type->cropbox_width * $type->cropbox_height));
                                    } else if ($type->get($size . '_image_resize_type') == '2') {
                                        $image->crop($type->get($size . '_image_width'), $type->get($size . '_image_height'));
                                    } else {
                                        $image->fit($type->get($size . '_image_width'), $type->get($size . '_image_height'));
                                    }

                                    if ('large' == $size && '' != $type->get('watermark_file_path')) {
                                        try {
                                            $watermark = new \App\Src\Http\File\File(ROOT_PATH_PROTECTED . DS . 'Storage' . DS . 'Watermarks' . DS . $type->get('watermark_file_path'));
                                        } catch (\App\Src\Http\File\FileNotFoundException $e) {
                                        }

                                        $image->addWatermark(ROOT_PATH_PROTECTED . DS . 'Storage' . DS . 'Watermarks' . DS . $type->get('watermark_file_path'), $type->get('watermark_transparency'), $file->type->get('watermark_position_vertical') . ' ' . $type->get('watermark_position_horizontal'), 5);
                                    }

                                    $image->save($model->$size()->getPath(), $type->get($size . '_image_quality'));
                                }
                            }

                            $model->save();
                            
                            \rename($path, $model->getPath());
                        }
                    }
                }
            }
        }

        return $hash;
    }

    public function render()
    {
        return view('form/field/dropzone', $this);
    }

    public function setValue($value)
    {
        if ($value == '') {
            $this->value = bin2hex(random_bytes(16));
        } else {
            $this->value = $value;
        }
    }

}
