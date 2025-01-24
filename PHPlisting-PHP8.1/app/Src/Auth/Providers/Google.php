<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Auth\Providers;

class Google
    implements ProviderInterface
{

    private $instance;

    public function __construct()
    {
        $this->instance = new \Google\Client();

        $this->instance->setClientId(config()->account->google_client_id);
        $this->instance->setClientSecret(config()->account->google_client_secret);
        $this->instance->setRedirectUri(route('account/callback/google'));

        $this->instance->addScope([
            'email',
            'profile',
        ]);
    }
    
    public function attempt()
    {
        if (null !== request()->get->get('code')) {
            $this->instance->authenticate(request()->get->code);

            $oauth = new \Google_Service_Oauth2($this->instance);

            try {
                $response = $oauth->userinfo->get();
            } catch (\Google\Service\Exception $e) {
                return false;
            }

            if (false !== isset($response['id'], $response['email'])) {
                $account = \App\Models\Account::where('provider', 'google')->where('unique_id', $response['id'])->first();

                if (null !== $account) {
                    return $account->user;
                } else {
                    return $this->create($response);
                }
            }
        }

        return false;
    }

    protected function create($user)
    {
        $userModel = new \App\Models\User();
        $userModel->first_name = $user['givenName'];
        $userModel->last_name = $user['familyName'];
        $userModel->email = $user['email'];
        $userModel->taxable = 1;
        $userModel->email_verified = 1;
        $userModel->added_datetime = date('Y-m-d H:i:s');
        $userModel->timezone = '+0000';

        if (null === config()->account->approval) {
            $userModel->active = 1;
        }

        $account = new \App\Models\Account();
        $account->provider = 'google';
        $account->usergroup_id = config()->account->default_group;
        $account->unique_id = $user['id'];

        $userModel->saveWithData(collect());
        
        $userModel->account()->save($account);

        return $userModel;
    }


    public function getLoginUrl()
    {
        return $this->instance->createAuthUrl();
    }

}
