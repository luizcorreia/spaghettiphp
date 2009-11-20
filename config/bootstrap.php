<?php
/**
 *  bootstrap.php é o arquivo responsável pela inicialização de toda a funcionalidade
 *  do Spaghetti*, como a inclusão de classes, definição de caminhos e carregamento
 *  de arquivos de configuração.
 *
 *  @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 *  @copyright Copyright 2008-2010, Spaghetti* Framework (http://spaghettiphp.org/)
 */

/**
 *  O Spaghetti* suporta apenas versões do PHP iguais ou superiores a 5.2. Caso
 *  esse requisito não seja satisfeito, um erro é emitido.
 */
if(version_compare(PHP_VERSION, '5.2') < 0):
    trigger_error('Spaghetti only works with PHP 5.2 or newer', E_USER_ERROR);
endif;

/**
  *  Inclui as definições de caminhos do Spaghetti*, presentes em /config/path.php.
  *  Caso você tenha instalado o Spaghetti* em uma estrutura de diretórios diferente
  *  do padrão, talvez seja necessário modificar os caminhos presentes nesse arquivo.
  */
require_once dirname(__FILE__) . '/paths.php';

/**
  *  Inclui classes básicas para o funcionamento do Spaghetti*.
  */
require_once 'core/Object.php';
require_once 'core/Loader.php';
import('core.Config');
import('core.debug.Debug');
import('core.debug.Log');
import('core.Mapper');

/**
  *  Inclui os arquivos de configuração da aplicação.
  */
import('config.settings');
import('config.routes');