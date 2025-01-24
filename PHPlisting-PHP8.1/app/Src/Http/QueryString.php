<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Http;

class QueryString
    extends \App\Src\Support\BaseCollection
{

    public function __construct(string $query)
    {
        $this->fromString($query);
    }

    public function fromString($query)
    {
        parse_str($query, $array);

        $this->collect($array);
    }

    public function __toString()
    {
        return http_build_query($this->toArray());
    }

}
