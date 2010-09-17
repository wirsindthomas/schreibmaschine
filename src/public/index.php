<?php
/**
 * Schreibmaschine
 *
 * LICENSE
 *
 * This source file is subject to the new CC-GNU LGPL
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/LGPL/2.1/
 *
 * @category   Schreibmaschine
 * @package    WST\Schreibmaschine
 * @version    $Id$
 * @copyright  Copyright (c) 2010 Alexander Thomas (at@wirsindthomas.com)
 * @license    http://creativecommons.org/licenses/LGPL/2.1/    GNU Lesser General Public License
 */

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(__DIR__ . '/application'));

// Define path to application directory
defined('LIBRARY_PATH')
    || define('LIBRARY_PATH', realpath(__DIR__ . '/library'));

// Define path to view directory
defined('VIEW_PATH')
    || define('VIEW_PATH', realpath(__DIR__ . '/themes'));

// Define path to plugin directory
defined('PLUGIN_PATH')
    || define('PLUGIN_PATH', realpath(__DIR__ . '/plugins'));

// Define path to config directory
defined('CONFIG_PATH')
    || define('CONFIG_PATH', realpath(__DIR__ . '/configs'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(LIBRARY_PATH),
    get_include_path(),
)));

/** Setup \Zend\Application */
require_once 'Zend/Application/Application.php';

// Create application, bootstrap, and run
$application = new Zend\Application\Application (
    APPLICATION_ENV,
    CONFIG_PATH . '/wst.schreibmaschine.config.ini'
);
$application->bootstrap()
            ->run();
