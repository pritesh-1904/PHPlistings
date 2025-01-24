<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Repositories;

class Statistics
{

    public function push($type, $id)
    {
        $array = [];

        foreach ((array) $id as $type_id) {
            $array[] = [
                'type' => $type,
                'type_id' => $type_id,
                'ip' => request()->ip(),
                'added_date' => date('Y-m-d'),
            ];
        }

        return db()->table('rawstats')->insert($array);
    }

    public function processDaily()
    {
        $stats = $this->getDaily();

        if ($stats->count() < 1) {
            return true;
        }

        foreach ($stats as $stat) {
            switch ($stat->type) {
                case 'listing_impression':
                    db()->table('listings')
                        ->where('id', $stat->type_id)
                        ->update(db()->raw('impressions = COALESCE(impressions, 0) + ' . $stat->total));
                    break;
                case 'listing_search_impression':
                    db()->table('listings')
                        ->where('id', $stat->type_id)
                        ->update(db()->raw('search_impressions = COALESCE(search_impressions, 0) + ' . $stat->total));
                    break;
                case 'listing_phone_view':
                    db()->table('listings')
                        ->where('id', $stat->type_id)
                        ->update(db()->raw('phone_views = COALESCE(phone_views, 0) + ' . $stat->total));
                    break;
                case 'listing_website_click':
                    db()->table('listings')
                        ->where('id', $stat->type_id)
                        ->update(db()->raw('website_clicks = COALESCE(website_clicks, 0) + ' . $stat->total));
                    break;
                case 'category_impression':
                    db()->table('categories')
                        ->where('id', $stat->type_id)
                        ->update(db()->raw('impressions = COALESCE(impressions, 0) + ' . $stat->total));
                    break;
                case 'location_impression':
                    db()->table('locations')
                        ->where('id', $stat->type_id)
                        ->update(db()->raw('impressions = COALESCE(impressions, 0) + ' . $stat->total));
                    break;
           }
        }
                    
        return db()->table('stats')->insert(
            $stats->each(function ($item, $key) {
                return [
                    'type' => $item->type,
                    'type_id' => $item->type_id,
                    'count' => $item->total,
                    'date' => $item->added_date,
                ];
            })
            ->all()
        );
    }

    private function getDaily()
    {
        return \App\Models\RawStat::query()
            ->where(db()->raw('added_date BETWEEN (CURDATE() - INTERVAL 1 DAY) AND (CURDATE() - INTERVAL 1 SECOND)'))
            ->select(db()->expr()->count('DISTINCT(ip)', 'total'))
            ->groupBy('type')
            ->groupBy('type_id')
            ->groupBy('added_date')
            ->get(['type', 'type_id', 'added_date']);
    }

}
