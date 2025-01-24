<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Gateway;

class SubscriptionNotification
    extends \App\Src\Gateway\BaseNotification
{

    const STATUS_CREATED = 'created';
    const STATUS_DELETED = 'deleted';

    private $subscriptionId = null;

    public function getSubscriptionId()
    {
        return $this->subscriptionId;
    }

    public function setSubscriptionId($value)
    {
        $this->subscriptionId = $value;

        return $this;
    }

}
