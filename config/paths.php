<?php
/**
 *  Paths.php mantém todos os caminhos de uma aplicação Spaghetti*, incluindo os
 *  mesmos em include_path.
 *
 *  @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 *  @copyright Copyright 2008-2010, Spaghetti* Framework (http://spaghettiphp.org/)
 */

define('DS', DIRECTORY_SEPARATOR);

/**
  *  Pasta raiz da aplicação.
  */
define('ROOT', dirname(dirname(__FILE__)));
/**
  *  Pasta contendo os arquivos de sistema.
  */
define('LIB', ROOT . DS . 'lib');
/**
  *  Pasta contendo o núcleo do Spaghetti*.
  */
define('CORE', LIB . DS . 'core');
/**
  *  Pasta contendo os arquivos da aplicação.
  */
define('APP', ROOT . DS . 'app');
/**
  *  Pasta contendo os arquivos de configuração da aplicação.
  */
define('CONFIG', ROOT . DS . 'config');
/**
  *  Host em que a aplicação se encontra.
  */
define('BASE_URL', 'http://' .  $_SERVER['HTTP_HOST']);

/**
  *  Inclui os caminhos do Spaghetti* em include_path.
  */
set_include_path(
    get_include_path() . PATH_SEPARATOR . LIB . PATH_SEPARATOR .
    APP . PATH_SEPARATOR . ROOT
);