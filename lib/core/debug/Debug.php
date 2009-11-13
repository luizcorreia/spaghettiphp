<?php
/**
 *  Debug fornece algumas funções úteis para debug, como definir error_reporting e
 *  mostrar representações legíveis de variáveis.
 *
 *  @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 *  @copyright Copyright 2008-2010, Spaghetti* Framework (http://spaghettiphp.org/)
 */

class Debug extends Object {
    /**
      *  Define error_reporting para mostrar erros de acordo com Debug.level.
      *
      *  @param integer $level Nível de erros a serem mostrados
      *  @return void
      */
    public static function errorReporting($level = null) {
        if(is_null($level)):
            $level = Config::read("Debug.level");
        endif;
        switch($level):
            case 3:
                $level = E_ALL | E_STRICT;
                break;
            case 2:
                $level = E_ALL | E_STRICT & ~E_NOTICE;
                break;
            case 1:
                $level = E_ALL & ~E_NOTICE & ~E_DEPRECATED;
                break;
            default:
                $level = 0;
        endswitch;
        ini_set("error_reporting", $level);
    }
    /**
      *  Formata e imprime o conteúdo de uma variável.
      *
      *  @param mixed $data Variável a ser impressa
      *  @return void
      */
    public static function pr($data) {
        echo "<pre>" . print_r($data, true) . "</pre>";
    }
    /**
      *  Formata e imprime o conteúdo parseável de uma variável.
      *
      *  @param mixed $data Variável a ser impressa
      *  @return void
      */
    public static function dump($data) {
        self::pr(var_export($data, true));
    }
}

/**
  *  Formata e imprime o conteúdo de uma variável.
  *
  *  @param mixed $data Variável a ser impressa
  *  @return void
  */
function pr($data) {
    Debug::pr($data);
}

/**
  *  Formata e imprime o conteúdo parseável de uma variável.
  *
  *  @param mixed $data Variável a ser impressa
  *  @return void
  */
function dump($data) {
    Debug::dump($data);
}