<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Http;

class Url
    extends \App\Src\Support\Collection
{

    protected $elements = [
        'scheme',
        'host',
        'port',
        'user',
        'pass',
        'path',
        'query',
        'fragment',
    ];

    public function __construct(string $url)
    {
        $this->fromString($url);
    }

    public function fromString($url)
    {
        if (false !== $components = parse_url($url)) {
            foreach ($this->elements as $element) {
                $this->put($element, ($element == 'query') ? new QueryString($components[$element] ?? '') : $components[$element] ?? '');
            }
        }
    }

    public function __clone()
    {
        $this->query = clone $this->query;
    }

    public function __toString()
    {
        return (($this->scheme != '') ? $this->scheme : 'http') . '://' . $this->host . (($this->port != '' && !in_array($this->port, [80, 443])) ? ':' . $this->port : '') . (($this->path != '') ? $this->path : '') . ((count($this->query) > 0) ? '?' . $this->query : '') . (($this->fragment != '') ? '#' . $this->fragment : '');
    }

}
