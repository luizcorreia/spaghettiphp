<?php
/**
 *  Short description.
 *
 *  @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 *  @copyright Copyright 2008-2009, Spaghetti* Framework (http://spaghettiphp.org/)
 */

defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
defined('ROOT') ? null : define('ROOT', dirname(dirname(__FILE__)));
defined('LIB') ? null : define('LIB', ROOT . DS . 'lib');
defined('CORE') ? null : define('CORE', LIB . DS . 'core');
defined('APP') ? null : define('APP', ROOT . DS . 'app');

defined('BASE_URL') ? null : define('BASE_URL', 'http://' .  $_SERVER["HTTP_HOST"]);