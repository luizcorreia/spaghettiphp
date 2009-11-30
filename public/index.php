<?php
/**
 *  Spaghetti* Framework.
 *
 *  @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 *  @copyright Copyright 2008-2010, Spaghetti* Framework (http://spaghettiphp.org/)
 */

/**
  *  Bem vindo ao Spaghetti*! Esse щ o front controller que receberс todas as
  *  requisiчѕes feitas р sua aplicaчуo, e estas serуo enviadas para Dispatcher
  *  para serem processadas e enviarem a resposta ao usuсrio.
  */

/**
  *  O arquivo incluэdo abaixo щ o responsсvel pela configuraчуo e inicializaчуo
  *  do Spaghetti*.
  */
require_once dirname(dirname(__FILE__)) . '/config/bootstrap.php';

/**
  *  Importa Dispatcher e dispara uma requisiчуo.
  */
import('core.Dispatcher');
Dispatcher::dispatch();