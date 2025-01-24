<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Auth\Providers;

class Facebook
    implements ProviderInterface
{

    private $instance;

    public function __construct()
    {
        $this->instance = new \Facebook\Facebook([
            'app_id' => config()->account->facebook_app_id,
            'app_secret' => config()->account->facebook_secret,
            'default_graph_version' => 'v2.10',
        ]);
    }

    public function attempt()
    {
        $helper = $this->instance->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken();
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if (!isset($accessToken)) {
            if ($helper->getError()) {
                header('HTTP/1.0 401 Unauthorized');
                echo "Error: " . $helper->getError() . "\n";
                echo "Error Code: " . $helper->getErrorCode() . "\n";
                echo "Error Reason: " . $helper->getErrorReason() . "\n";
                echo "Error Description: " . $helper->getErrorDescription() . "\n";
            } else {
                header('HTTP/1.0 400 Bad Request');
                echo 'Bad request';
            }
            exit;
        }

        $oAuth2Client = $this->instance->getOAuth2Client();
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);
        $tokenMetadata->validateAppId(config()->account->facebook_app_id);
        $tokenMetadata->validateExpiration();

        if (!$accessToken->isLongLived()) {
            try {
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
                exit;
            }
        }

        try {
            $response = $this->instance->get('/me?fields=id,first_name,last_name,email', (string) $accessToken);
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        $user = $response->getGraphUser();

        if (null !== $user->getId() && null !== $user->getEmail()) {
            $account = \App\Models\Account::where('provider', 'facebook')->where('unique_id', $user->getId())->first();

            if (null !== $account) {
                return $account->user;
            } else {
                return $this->create($user);
            }
        }        

        return false;
    }

    protected function create($user)
    {
        $userModel = new \App\Models\User();
        $userModel->first_name = $user->getFirstName();
        $userModel->last_name = $user->getLastName();
        $userModel->email = $user->getEmail();
        $userModel->taxable = 1;
        $userModel->email_verified = 1;
        $userModel->added_datetime = date('Y-m-d H:i:s');
        $userModel->timezone = '+0000';

        if (null === config()->account->approval) {
            $userModel->active = 1;
        }

        $account = new \App\Models\Account();
        $account->provider = 'facebook';
        $account->usergroup_id = config()->account->default_group;
        $account->unique_id = $user->getId();

        $userModel->saveWithData(collect());
        
        $userModel->account()->save($account);

        return $userModel;
    }


    public function getLoginUrl()
    {
        $helper = $this->instance->getRedirectLoginHelper();
        $permissions = ['email', 'public_profile'];

        return $helper->getLoginUrl(route('account/callback/facebook'), $permissions);
    }

}
