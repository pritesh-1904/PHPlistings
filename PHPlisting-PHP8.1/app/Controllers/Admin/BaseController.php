<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class BaseController
    extends \App\Src\Mvc\BaseController
{

    public function __construct()
    {
        layout()
            ->setHeader('admin/header')
            ->setFooter('admin/footer')
            ->setWrapper('admin/wrapper');
    }

    public function action($name, array $arguments = [])
    {        
        $action = 'action' . $name;

        if (method_exists($this, $action)) {
            if (false === auth()->check()) {
                return redirect(adminRoute('login'))
                    ->with('return', request()->url());
            }

            if ('1' != auth()->user()->id) {
                if (false === auth()->check('admin_login')) {
                    return redirect(adminRoute('login'))
                        ->with('error', view('flash/error', ['message' => [__('account.alert.login.failure')]]))
                        ->with('return', request()->url());
                }

                if (null !== auth()->user()->banned || false !== request()->isBanned()) {
                    auth()->logout();

                    return redirect(adminRoute('login'))
                        ->with('error', view('flash/error', ['message' => [__('admin.alert.banned_user')]]));
                }

                if (null === auth()->user()->email_verified) {
                    auth()->logout();

                    return redirect(adminRoute('login'))
                        ->with('error', view('flash/error', ['message' => [__('admin.alert.unverified_user')]]));
                }

                if (null === auth()->user()->active) {
                    auth()->logout();

                    return redirect(adminRoute('login'))
                        ->with('error', view('flash/error', ['message' => [__('admin.alert.unapproved_user')]]));
                }
            }

            return $this->{$action}($arguments);
        } else {
            throw new \Exception('Action "' . $action . '" not found.');
        }
    }

}
