<?php
/**
 *  Short description.
 *
 *  @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 *  @copyright Copyright 2008-2009, Spaghetti* Framework (http://spaghettiphp.org/)
 */

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(__FILE__)));
define('LIB', ROOT . DS . 'lib');
define('CORE', LIB . DS . 'core');
define('APP', ROOT . DS . 'app');
define('BASE_URL', 'http://' .  $_SERVER["HTTP_HOST"]);

set_include_path(get_include_path() . PATH_SEPARATOR . LIB . PATH_SEPARATOR . APP);