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
define('ROOT', dirname(dirname(__FILE__)));

/**
  *  Pasta contendo os arquivos de sistema.
  */
define('LIB', ROOT . '/lib');

/**
  *  Pasta contendo o núcleo do Spaghetti*.
  */
define('CORE', LIB . '/core');

/**
  *  Pasta contendo os arquivos da aplicação.
  */
define('APP', ROOT . '/app');

/**
  *  Pasta contendo os arquivos de configuração da aplicação.
  */
define('CONFIG', ROOT . '/config');

/**
  *  Define o host em que a aplicação se encontra.
  */
if(isset($_SERVER['HTTP_HOST'])):
    $https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on';
    define('BASE_URL', 'http' . ($https ? 's' : '') . '://' . $_SERVER['HTTP_HOST']);
endif;

/**
  *  Inclui os caminhos do Spaghetti* em include_path.
  */
set_include_path(
    get_include_path() . PATH_SEPARATOR . LIB . PATH_SEPARATOR .
    APP . PATH_SEPARATOR . ROOT
);