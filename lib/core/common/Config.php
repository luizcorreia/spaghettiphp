<?php
/**
 *  Config é a classe que toma conta da leitura e escrita de configurações do Spaghetti*.
 *
 *  @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 *  @copyright Copyright 2008-2010, Spaghetti* Framework (http://spaghettiphp.org/)
 */

class Config extends Object {
    /**
     *  Lista de configurações.
     */
    protected static $config = array();

    /**
     *  Retorna o valor de uma chave de configuração.
     *
     *  @param string $key Chave de configuração a ser retornada
     *  @return mixed Valor da respectiva chave de configuração
     */
    public static function read($key) {
        return self::$config[$key];
    }
    /**
     *  Grava um valor em uma chave de configuração.
     *
     *  @param string $key Chave de configuração a ser gravada
     *  @param string $value Valor a ser gravado
     *  @return void
     */
    public static function write($key, $value) {
        self::$config[$key] = $value;
    }
}