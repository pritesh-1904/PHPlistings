<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Dbal;

class Collection
    extends \App\Src\Support\Collection
{

    private $limit;
    private $total;

    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function links($template = 'misc/pagination')
    {
        return view($template, ['results' => $this->count(), 'total' => $this->total, 'limit' => $this->limit]);
    }

}
