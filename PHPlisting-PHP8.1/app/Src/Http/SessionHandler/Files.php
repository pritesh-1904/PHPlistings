<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Http\SessionHandler;

class Files
    extends \SessionHandler
    implements \SessionHandlerInterface
{

    public function __construct()
    {
        ini_set('session.gc_probability', 1);
        ini_set('session.save_path', config()->session->path);
        ini_set('session.save_handler', 'files');
    }

    #[\ReturnTypeWillChange]
    public function read($session_id)
    {
        $data = parent::read($session_id);
        if (false !== $data) {
            if (false !== $session_data = \App\Src\Support\Crypt::decode($data, config()->security->encryption_key, config()->security->authentication_key)) {
                return $session_data;
            }
        }

        return '';
    }

    #[\ReturnTypeWillChange]
    public function write($session_id, $session_data)
    {
        return parent::write(
            $session_id, 
            \App\Src\Support\Crypt::encode($session_data, config()->security->encryption_key, config()->security->authentication_key)
        );
    }

}
