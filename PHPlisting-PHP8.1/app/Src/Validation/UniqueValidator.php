<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Validation;

class UniqueValidator
    implements ValidatorInterface
{

    protected $table;
    protected $field;
    protected $id;
    protected $primaryKey;

    public function __construct($table = null, $field = null, $id = null, $primaryKey = 'id')
    {
        $this->table = $table;
        $this->field = $field;
        $this->id = $id;
        $this->primaryKey = $primaryKey;
    }

    public function validate($value, $context = null)
    {
        if (null !== $this->table && null !== $this->field) {
            if ('' !== trim($value)) {
                $query = db()->table($this->table)
                    ->where($this->field, $value);

                if (isset($this->id)) {
                    $query->where($this->primaryKey, '!=', $this->id);
                }

                if ($query->count() > 0) {
                    throw new ValidatorException(__('form.validation.unique'));
                }
            }
        }
    }

}
