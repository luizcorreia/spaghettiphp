<?php
/**
 *  Short description.
 *
 *  @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 *  @copyright Copyright 2008-2010, Spaghetti* Framework (http://spaghettiphp.org/)
 */

require_once '../lib/core/Bootstrap.php';

import('core.Dispatcher');

$dispatcher = new Dispatcher;
$dispatcher->dispatch();