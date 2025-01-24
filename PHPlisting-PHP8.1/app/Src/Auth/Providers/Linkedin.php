<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Auth\Providers;

class Linkedin
    implements ProviderInterface
{

    private $instance;

    public function __construct()
    {
        $this->instance = new \LinkedIn\Client(
            '77o6f5gjajvgt1',
            'xv0JjZ9UljSFIWYN'
        );
    }

    public function attempt()
    {
        if (null === request()->get->get('code')) {
            return false;
        }

        $this->instance->setRedirectUrl(route('account/callback/linkedin'));

        $accessToken = $this->instance->getAccessToken(request()->get->get('code'));

        $user = $this->instance->get(
            'me',
            [
                'fields' => 'id,firstName,lastName',
            ]
        );

        $email = $this->instance->get(
            'emailAddress',
            [
                'q' => 'members',
                'projection' => '(elements*(handle~))',
            ]
        );

        $user['email'] = $email;

        if (array_key_exists('id', $user)) {
            $account = \App\Models\Account::where('provider', 'linkedin')
                ->where('unique_id', $user['id'])
                ->first();

            if (null !== $account) {
                return $account->user;
            } elseif (false !== $userModel = $this->create($user)) {
                return $userModel;
            }
        }        

        return false;
    }

    protected function create($user)
    {
        $userModel = new \App\Models\User();
        $userModel->first_name = $user['firstName']['localized']['en_US'] ?? 'First Name';
	$userModel->last_name = $user['lastName']['localized']['en_US'] ?? 'Last Name';
	$userModel->email = $user['email']['elements'][0]['handle~']['emailAddress'] ?? null;
	$userModel->taxable = 1;
	$userModel->added_datetime = date('Y-m-d H:i:s');
	$userModel->email_verified = 1;
	$userModel->timezone = '+0100';

	if (null === config()->account->approval) {
	    $userModel->active = 1;
	}

        $account = new \App\Models\Account();
	$account->provider = 'linkedin';
	$account->usergroup_id = config()->account->default_group;
	$account->unique_id = $user['id'];

	$userModel->saveWithData(collect());

        $userModel->account()->save($account);

        return $userModel;
    }


    public function getLoginUrl()
    {
        $scopes = [
            \LinkedIn\Scope::READ_LITE_PROFILE, 
            \LinkedIn\Scope::READ_EMAIL_ADDRESS,
        ];

        $this->instance->setRedirectUrl(route('account/callback/linkedin'));

        return $this->instance->getLoginUrl($scopes);
    }

}
