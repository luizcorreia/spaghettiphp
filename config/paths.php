<?php
/**
 *  paths.php contém os caminhos de uma aplicação Spaghetti*, definindo-os em
 *  include_path.
 *
 *  @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 *  @copyright Copyright 2008-2010, Spaghetti* Framework (http://spaghettiphp.org/)
 */

/**
  *  Esse é o caminho da pasta principal onde sua aplicação está instalada.
  */
define('SPAGHETTI_ROOT', dirname(dirname(__FILE__)));

/**
  *  Esse é o caminho onde estão todos os arquivos de sua aplicação.
  */
define('SPAGHETTI_APP', ROOT . '/app');

/**
  *  Inclui os caminhos do Spaghetti* em include_path. Caso você tenha instalado
  *  o Spaghetti* em uma estrutura de diretórios diferente do padrão, você precisará
  *  alterar os caminhos abaixo para que eles confiram com a estrutura de arquivos
  *  de sua instalação.
  */
$path = array();
$path []= SPAGHETTI_APP;
$path []= SPAGHETTI_ROOT . '/lib';
$path []= SPAGHETTI_ROOT;
$path []= get_include_path();
$path = implode(PATH_SEPARATOR, $path);
set_include_path($path);