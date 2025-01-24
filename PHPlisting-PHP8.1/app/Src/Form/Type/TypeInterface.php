<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

interface TypeInterface

{

    public function __construct($name, array $options = [], \App\Src\Form\Builder $form = null);
    public function getForm();
    public function getLabel();
    public function getName();
    public function setValue($value);
    public function getValue();
    public function sanitize($value);
    public function render();

}
