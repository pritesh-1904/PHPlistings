<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Auth\Providers;

class Twitter
    implements ProviderInterface
{

    private $instance;

    public function __construct()
    {
        $this->instance = new \Abraham\TwitterOAuth\TwitterOAuth(config()->account->twitter_key, config()->account->twitter_secret);
    }
    
    public function attempt()
    {
        if (null !== request()->get->get('oauth_token')) {
            $access_token = $this->instance->oauth('oauth/access_token', [
                'oauth_verifier' => request()->get->get('oauth_verifier'),
                'oauth_token' => request()->get->get('oauth_token'),
            ]);

            $connection = new \Abraham\TwitterOAuth\TwitterOAuth(config()->account->twitter_key, config()->account->twitter_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);

            $reponse = $connection->get('account/verify_credentials', [
                'include_email' => 'true',
                'include_entities' => 'false',
                'skip_status' => 'true',
            ]);

            if (false !== isset($response['id'], $response['email'])) {
                $account = \App\Models\Account::where('provider', 'twitter')->where('unique_id', $response['id'])->first();

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
        $userModel->first_name = $user['name'] ?? '';
        $userModel->email = $user['email'];
        $userModel->taxable = 1;
        $userModel->email_verified = 1;
        $userModel->added_datetime = date('Y-m-d H:i:s');
        $userModel->timezone = '+0000';

        if (null === config()->account->approval) {
            $userModel->active = 1;
        }

        $account = new \App\Models\Account();
        $account->provider = 'twitter';
        $account->usergroup_id = config()->account->default_group;
        $account->unique_id = $user['id'];

        $userModel->saveWithData(collect());
        
        $userModel->account()->save($account);

        return $userModel;
    }


    public function getLoginUrl()
    {
        $temporary_credentials = $this->instance->oauth('oauth/request_token', ['oauth_callback' => route('account/callback/twitter')]);

        return $this->instance->url('oauth/authenticate', ['oauth_token' => $temporary_credentials['oauth_token']]);
    }

}
