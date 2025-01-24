<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Validation;

class HtmlmaxtagsValidator
    implements ValidatorInterface
{

    protected $tag;
    protected $value;

    public function __construct($tag, $value = null)
    {
        $this->tag = $tag;

        if (null === $value || '' == $value) {
            $this->value = 0;
        } else {
            $this->value = $value;
        }
    }

    public function validate($value, $context = null)
    {
        if (null !== $value && '' !== $value) {
            $dom = new \DOMDocument();

            if (false === @$dom->loadHTML(purify(d($value)))) {
                throw new ValidatorException(__('form.validation.html.parse'));
            }

            $tags = $dom->getElementsByTagName($this->tag);

            if ($tags->length > $this->value) {
                throw new ValidatorException(__('form.validation.htmlmaxtags.' . $this->tag, ['tag' => $this->tag, 'value' => $this->value]));
            }
        }
    }

    public function getTagParameter()
    {
        return $this->tag;
    }

    public function getValueParameter()
    {
        return $this->value;
    }

}
