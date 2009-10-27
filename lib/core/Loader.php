<?php
/**
 *  Loader é a classe responsável pelas tarefas de carregamento de arquivos no
 *  Spaghetti*.
 *
 *  @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 *  @copyright Copyright 2008-2009, Spaghetti* Framework (http://spaghettiphp.org/)
 */

class Loader {
    /**
      *  Importa um arquivo.
      *
      *  @param string $file Nome do arquivo a ser carregado
      *  @param string $extension Extensão do arquivo
      *  @return boolean Retorno da importação do arquivo
      */
    public static function import($file, $extension = 'php') {
        $file = str_replace('.', DS, $file);
        return require_once $file . '.' . $extension;
    }
}

/**
  *  Importa um arquivo.
  *
  *  @param string $file Nome do arquivo a ser carregado
  *  @param string $extension Extensão do arquivo
  *  @return boolean Retorno da importação do arquivo
  */
function import($file, $extension = 'php') {
    return Loader::import($file, $extension);
}