<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2022 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Mvc;

class Layout
    extends \App\Src\Support\Collection
{

    protected $header = 'header';
    protected $footer = 'footer';
    protected $wrapper = 'wrapper';

    protected $title = '';
    protected $meta;
    protected $canonical;
    protected $css;
    protected $js;
    protected $footerJs;

    public function __construct()
    {
        $this->meta = collect();
        $this->css = collect();
        $this->js = collect();
        $this->footerJs = collect();
    }

    public function setTitle($title, array $replacements = null)
    {
        $this->title = $this->replace($title, $replacements);

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function addCss($content = null, $sortId = null)
    {
        $this->css->put($sortId ?? sha1($content), $content);

        return $this;
    }

    public function getCss()
    {
        return implode("\n", $this->css->all()) . "\n";
    }

    public function addJs($content = null, $sortId = null)
    {
        $this->js->put($sortId ?? sha1($content), $content);

        return $this;
    }

    public function getJs()
    {
        return implode("\n", $this->js->all()) . "\n" . d(config()->general->custom_js ?? '');
    }

    public function addFooterJs($content = null, $sortId = null)
    {
        $this->footerJs->put($sortId ?? sha1($content), $content);

        return $this;
    }

    public function getFooterJs()
    {
        return implode("\n", $this->footerJs->all()) . "\n";
    }

    public function setWrapper($template = null)
    {
        $this->wrapper = $template;

        return $this;
    }

    public function setHeader($template = null)
    {
        $this->header = $template;

        return $this;
    }

    public function setFooter($template = null)
    {
        $this->footer = $template;

        return $this;
    }

    public function setMeta($name, $content, array $replacements = null, $type = 'name')
    {
        $this->meta->push(collect([
            'type' => $type,
            'name' => $name,
            'content' => $this->replace($content, $replacements),
        ]));

        return $this;
    }

    public function setMetaIfEmpty($name, $content, array $replacements = null, $type = 'name')
    {
        $meta = $this->meta->where('type', $type)->where('name', $name)->first();

        if (null === $meta || '' == $meta->get('content') || (is_collection($meta) && $meta->count() == 0)) {
            return $this->setMeta(...func_get_args());
        }

        return $this;
    }

    public function setMetaProperty($name, $content, array $replacements = null)
    {
        return $this->setMeta($name, $content, $replacements, 'property');
    }

    public function getMeta()
    {        
        return $this->meta;
    }

    public function setCanonicalRoute($route, \App\Src\Support\Collection $query)
    {
        $this->canonical = route($route, ['keyword' => $query->get('keyword'), 'page' => ((1 == $query->get('page')) ? null : $query->get('page'))]);

        return $this;
    }

    public function getCanonicalUrl()
    {
        return $this->canonical;
    }

    public function offsetSet($offset, $value): void
    {
        parent::offsetSet($offset, (string) $value);
    }

    public function content($content = null)
    {
        return 
            view($this->header, $this->all()) . 
            view($this->wrapper, array_merge($this->all(), ['content' => $content])) . 
            view($this->footer, $this->all());
    }


    private function replace($string, array $replacements = null) {
        if (null !== $replacements) {
            foreach ($replacements as $key => $value) {
                if (strstr($string ?? '', '{' . $key. '}')) {
                    $string = preg_replace('/{' . $key . '}/u', str_replace('$', '\$', $value), $string);
                }
            }
        }

        $string = preg_replace('/{(.*)}/s', '', $string ?? '');

        return $string;
    }

}
