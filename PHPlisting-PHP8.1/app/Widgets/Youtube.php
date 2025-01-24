<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Youtube
    extends \App\Src\Widget\BaseWidget
{

    public function isMultiInstance()
    {
        return true;
    }

    public function render()
    {
        $url = null;
        
        if ('' != $this->getSettings()->get('youtube_video_url', '')) {
            $url = $this->getSettings()->get('youtube_video_url');
        }

        if ('' != $this->getSettings()->get('field_id', '')) {
            if (null !== $this->getData()->get('listing') && false !== ($this->getData()->get('listing') instanceof \App\Models\Listing)) {
                if (null !== $field = \App\Models\ListingField::where('id', $this->getSettings()->get('field_id'))->where('type', 'youtube')->where('type_id', $this->getData()->get('listing')->type_id)->first()) {
                    $data = $this->getData()->get('listing')->data->where('field_name', $field->name)->first();

                    if (null !== $data && null !== $data->get('active') && '' != $data->get('value', '')) {
                        $url = $data->get('value');
                    }
                }
            }
        }

        if (null === $url) {
            return null;
        }

        $id = null;
        
        $regex = '/(?im)\b(?:https?:\/\/)?(?:w{3}\.)?youtu(?:be)?\.(?:com|be)\/(?:(?:\??v=?i?=?\/?)|watch\?vi?=|watch\?.*?&v=|embed\/|)([A-Z0-9_-]{11})\S*(?=\s|$)/';

        if (false !== preg_match_all($regex, $url, $matches, PREG_SET_ORDER)) {
            if (false !== isset($matches[0]) && false !== isset($matches[0][1])) {
                $id = $matches[0][1];
            }
        }

        if (null === $id) {
            return null;
        }

        $this->rendered = true;

        return view('widgets/youtube', [
            'settings' => $this->getSettings(),
            'data' => $this->getData(),
            'youtube_video_id' => $id,
        ]);
    }

    public function getDefaultSettings()
    {
        return collect([
            'youtube_video_url' => '',
            'field_id' => '',
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('youtube_video_url', 'youtube', ['label' => __('widget.youtube.form.label.youtube_video_url')])
            ->add('field_id', 'select', ['label' => __('widget.youtube.form.label.field_name'), 'options' => ['' => ''] + $this->getYoutubeFields()]);
    }

    public function getYoutubeFields()
    {
        $fields = \App\Models\ListingField::where('type', 'youtube')
            ->with('listingType')
            ->get();
        
        return $fields->pluck(function ($field) {
            return $field->listingType->name_plural . ' / ' . $field->label;
        }, 'id')->all();
    }

}
