<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Repositories;

class Rss
{

    private $listings;

    public function __construct()
    {
        $this->listings = collect();
    }

    public function push(\App\Models\Listing $listing)
    {
        $this->listings->push($listing);
        
        return $this;
    }
        
    public function render($link, $title, $description)
    {
        $response = 
            '<?xml version="1.0" encoding="' . config()->app->charset . '"?>' . "\n" .
            '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n" .
            '   <channel>' . "\n" .
            '       <atom:link href="' . $link . '" rel="self" type="application/rss+xml" />' . "\n" .
            '       <title>' . e(d($title)) . '</title>' . "\n" .
            '       <link>' . $link . '</link>' . "\n" .
            '       <description>' . e(d($description)) . '</description>' . "\n" .
            '       <language>' . locale()->getLocale() . '</language>' . "\n";

        foreach ($this->listings as $listing) {
            $response .= 
                '       <item>' . "\n" .
                '           <title>' . e(d($listing->title)) . '</title>' . "\n" .
                '           <description>' . e(d(\mb_strimwidth(\strip_tags(d($listing->description)), 0, $listing->get('_description_size') ?? 0, '...'))) . '</description>' . "\n";

            if (null !== $listing->get('_page')) {
                $response .=
                    '           <link>' . route($listing->type->slug . '/' . $listing->slug) . '</link>' . "\n";
            }
                
            $response .=
                '           <guid isPermaLink="false">' . md5($listing->id . ((null !== $listing->get('event_date')) ? $listing->event_date : '')) . '</guid>' . "\n" .
                '           <pubDate>' . locale()->formatDatetimeRFC822($listing->get('added_datetime')) . '</pubDate>' . "\n";

            $field = $listing->data->where('field_name', 'logo_id')->where('value', '!=', '')->first();

            if (null !== $field && null !== $field->get('active') && null !== $logo = \App\Models\File::where('document_id', $field->value)->where('uploadtype_id', 1)->first()) {
                $response .= 
                    '           <enclosure url="' . $logo->getUrl() . '" length="' . $logo->size . '" type="' . $logo->mime . '"/>' . "\n";
            }

            $response .= 
                '           <category>' . e(d($listing->category->name)) . '</category>' . "\n" .
                '       </item>' . "\n";
        }

        $response .= 
            '   </channel>' . "\n" .
            '</rss>' . "\n";

        return $response;
    }

}
