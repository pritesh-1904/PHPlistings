<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Auth\Providers;

class Native
    implements ProviderInterface
{

    public function attempt(array $credentials = [])
    {
        if (!isset($credentials['password'])) {
            return false;
        }

        $query = \App\Models\User::query();

        $password = $credentials['password'];

        unset($credentials['password']);

        foreach ($credentials as $name => $value) {
            $query->where($name, $value);
        }

        $user = $query->first();

        if (null !== $user && $user->account->provider == 'native' && $user->account->verifyPassword($password)) {
            return $user;
        }

        return false;
    }

    public function getLoginUrl()
    {
        return null;
    }

}
