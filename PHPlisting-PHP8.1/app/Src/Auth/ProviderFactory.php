<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Auth;

class ProviderFactory
{

    protected $user;
    protected $provider;

    protected $request;
    protected $response;

    public function __construct(\App\Src\Http\Request $request, \App\Src\Http\Response $response)
    {
        $this->request = $request;
        $this->response = $response;
        
        if (!session()->has('authentication')) {
            if (null !== $request->cookie('authentication')) {
                if (null !== $user = \App\Models\User::where('token', $request->cookie('authentication'))->first()) {
                    $this->login($user, true);
                }
            }
        } else if (null !== $user = \App\Models\User::where('id', session()->get('authentication'))->first()) {
            $user->account->last_activity_datetime = date('Y-m-d H:i:s');
            $user->account->save();

            $this->user = $user;
        }
    
    }

    public function check($roles = null)
    {
        if ($this->user instanceof \App\Models\User) {
            if (1 == $this->user->id) {
                return true;
            }

            if (null !== $roles) {
                if (is_array($roles)) {
                    if (count(array_diff($roles, $this->user->account->group->roles->pluck('name', 'id')->all())) > 0) {
                        return false;
                    }
                } else {
                    if ($this->user->account->group->roles->where('name', $roles)->count() == 0) {
                        return false;
                    }
                }
            }

            return true;
        }

        return false;
    }

    public function attempt(array $parameters = null, $remember = false, $roles = null)
    {
        if (false !== $user = $this->getProvider()->attempt($parameters)) {
            if (null === $roles || false !== $this->check($roles)) {
                return $this->login($user, $remember);
            }
        }

        return false;
    }

    public function login(\App\Models\User $user, $remember = false)
    {
        $user->account->last_session_datetime = $user->account->last_activity_datetime;
        $user->account->last_activity_datetime = date('Y-m-d H:i:s');
        $user->account->ip = request()->ip();
        $user->account->save();

        $this->user = $user;

        $this->request->session()->put('authentication', $user->get($user->getPrimaryKey()));

        if ($remember) {
            $this->response->cookie('authentication', $user->token, 60*24*30, '/' . trim(request()->basePath(), '/'));
        }

        return true;
    }

    public function logout()
    {
        session()->forget('authentication');

        $this->response->cookie('authentication', '', 0, '/' . trim(request()->basePath(), '/'));

        return $this;
    }

    public function user()
    {
        return $this->user;
    }

    public function getLoginUrl()
    {
        return $this->getProvider()->getLoginUrl();
    }

    public function setProvider($provider)
    {
        $class = '\\App\\Src\\Auth\\Providers\\' . ucfirst(strtolower($provider));

        if (class_exists($class) && is_subclass_of($class, '\\App\\Src\\Auth\\Providers\\ProviderInterface')) {
            $this->provider = new $class();
        } else {
            throw new \Exception('Authentication provider "' . $provider . '" is not supported');
        }
    }

    protected function getProvider()
    {
        return $this->provider;
    }

}
