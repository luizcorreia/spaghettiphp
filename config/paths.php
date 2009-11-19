<?php
/**
 *  Paths.php mantém todos os caminhos de uma aplicação Spaghetti*, incluindo os
 *  mesmos em include_path.
 *
 *  @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 *  @copyright Copyright 2008-2010, Spaghetti* Framework (http://spaghettiphp.org/)
 */

/**
  *  Pasta raiz da aplicação.
  */
define('SPAGHETTI_ROOT', dirname(dirname(__FILE__)));

/**
  *  Pasta contendo os arquivos da aplicação.
  */
define('SPAGHETTI_APP', ROOT . '/app');

/**
  *  Inclui os caminhos do Spaghetti* em include_path.
  */
$path = array();
$path []= SPAGHETTI_APP;
$path []= SPAGHETTI_ROOT . '/lib';
$path []= SPAGHETTI_ROOT;
$path []= get_include_path();
$path = implode(PATH_SEPARATOR, $path);
set_include_path($path);