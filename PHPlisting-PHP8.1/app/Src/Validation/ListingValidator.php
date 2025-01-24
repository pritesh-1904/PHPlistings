<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Validation;

class ListingValidator
    implements ValidatorInterface
{

    public $type_id = null;

    public function __construct($type_id = null)
    {
        if (null !== $type_id) {
            $this->type_id = $type_id;
        }
    }

    public function validate($value, $context = null)
    {
        if (null !== $value && '' != $value) {
            $query = \App\Models\Listing::where('id', $value);

            if (null !== $this->type_id && '' != $this->type_id) {
                $query->where('type_id', $this->type_id);
            }

            $listing = $query->first();

            if (null === $listing || (false !== auth()->check() && false === auth()->check('admin_login') && $listing->user_id != auth()->user()->id)) {
                throw new ValidatorException(__('form.validation.listing'));
            }
        }
    }

}
