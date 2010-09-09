<?php

// define the root directory
define('SPAGHETTI_ROOT', dirname(dirname(__FILE__)));

// add the root directory to the include path
set_include_path(SPAGHETTI_ROOT . PATH_SEPARATOR . get_include_path());

// include core.common
require 'lib/core/common/Config.php';
require 'lib/core/common/Inflector.php';
require 'lib/core/common/Utils.php';
require 'lib/core/common/Exceptions.php';
require 'lib/core/common/String.php';
require 'lib/core/common/Filesystem.php';
require 'lib/core/common/Hookable.php';
require 'lib/core/common/Validation.php';

// include and initialize core.debug
require 'lib/core/debug/Debug.php';

/**
 * Debug::errorHandler() can cause some trouble, so it's disabled by default.
 * Uncomment the following line if you want your errors to throw exceptions.
 */
// Debug::errorHandler();

// include core.dispatcher
require 'lib/core/dispatcher/Dispatcher.php';
require 'lib/core/dispatcher/Mapper.php';
// require 'lib/core/dispatcher/OldMapper.php';

// include core.model
require 'lib/core/model/Model.php';

// include core.controller
require 'lib/core/controller/Controller.php';

// includes core.view
require 'lib/core/view/View.php';

// include core.storage
require 'lib/core/storage/Cookie.php';
require 'lib/core/storage/Session.php';

// include core.security
require 'lib/core/security/Security.php';
require 'lib/core/security/Sanitize.php';

// include application's files
require 'app/controllers/app_controller.php';
require 'app/models/app_model.php';