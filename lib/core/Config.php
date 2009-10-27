<?php
/**
 *  Loader é a classe responsável pelas tarefas de carregamento de arquivos no
 *  Spaghetti*.
 *
 *  @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 *  @copyright Copyright 2008-2010, Spaghetti* Framework (http://spaghettiphp.org/)
 */

/**
 *  Config é a classe que toma conta de todas as configurações necessárias para
 *  uma aplicação do Spaghetti.
 */

class Config {
    /**
     *  Definições de configurações.
     *
     *  @var array
     */
    private $config = array();
    /**
     *  Retorna uma única instância (Singleton) da classe solicitada.
     *
     *  @staticvar object $instance Objeto a ser verificado
     *  @return object Objeto da classe utilizada
     */
    public static function &getInstance() {
        static $instance = array();
        if(!isset($instance[0]) || !$instance[0]):
            $instance[0] = new Config();
        endif;
        return $instance[0];
    }
    /**
     *  Retorna o valor de uma determinada chave de configuração.
     *
     *  @param string $key Nome da chave da configuração
     *  @return mixed Valor de configuração da respectiva chave
     */
    public static function read($key = "") {
        $self = self::getInstance();
        return $self->config[$key];
    }
    /**
     *  Grava o valor de uma configuração da aplicação para determinada chave.
     *
     *  @param string $key Nome da chave da configuração
     *  @param string $value Valor da chave da configuração
     *  @return boolean true
     */
    public static function write($key = "", $value = "") {
        $self = self::getInstance();
        $self->config[$key] = $value;
        return true;
    }
}
