<?php 

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2024 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

define('DS', DIRECTORY_SEPARATOR);
define('PATH', __DIR__);
define('ROOT_PATH', realpath(__DIR__ . '/../'));
define('ROOT_PATH_PROTECTED', ROOT_PATH . DS . 'app');

ini_set('display_errors', '0');

error_reporting(E_ALL);

date_default_timezone_set('UTC');

require ROOT_PATH . '/vendor/autoload.php';

\App\Src\Application::getInstance()->execute();
