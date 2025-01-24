<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Account;

class BaseController
    extends \App\Src\Mvc\BaseController
{

    public function __construct()
    {
        layout()->setWrapper('account/wrapper');
    }

    public function action($name, array $arguments = [])
    {        
        $action = 'action' . $name;

        if (method_exists($this, $action)) {
            if (false === auth()->check('user_login')) {

//                session()->put('return', request()->url());
                
                return redirect(route('account/login'))
                    ->with('return', request()->url());
            }

            if ('1' != auth()->user()->id) {
                if (null !== auth()->user()->banned || false !== request()->isBanned()) {
                    auth()->logout();

                    return redirect(route('account/login'))
                        ->with('error', view('flash/error', ['message' => [__('account.alert.banned_user')]]));
                }

                if (null === auth()->user()->email_verified) {
                    auth()->logout();

                    return redirect(route('account/login'))
                        ->with('error', view('flash/error', ['message' => [__('account.alert.unverified_user')]]));
                }

                if (null === auth()->user()->active) {
                    auth()->logout();

                    return redirect(route('account/login'))
                        ->with('error', view('flash/error', ['message' => [__('account.alert.unapproved_user')]]));
                }
            }

            return $this->{$action}($arguments);
        } else {
            throw new \Exception('Action "' . $action . '" not found.');
        }
    }

}
