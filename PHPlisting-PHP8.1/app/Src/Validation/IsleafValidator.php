<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Validation;

class IsleafValidator
    implements ValidatorInterface
{

    protected $table;
    protected $primaryKey;

    public function __construct($table, $primaryKey = 'id')
    {
        $this->table = $table;
        $this->primaryKey = $primaryKey;
    }

    public function validate($value, $context = null)
    {
        if (null !== $value && '' !== $value) {

            $node = db()->table($this->table)
                ->where($this->primaryKey, $value)
                ->first(['_left', '_right']);

            if (null === $node || $node->get('_right') - $node->get('_left') != 1) {
                throw new ValidatorException(__('form.validation.isleaf'));
            }
        }
    }

}
