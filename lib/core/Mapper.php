<?php
/**
 *  Mapper é o responsável por cuidar de URLs e roteamento dentro do Spaghetti*.
 *
 *  @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 *  @copyright Copyright 2008-2010, Spaghetti* Framework (http://spaghettiphp.org/)
 */

import('core.utils.String');

class Mapper extends Object {
    /**
     *  Definições de prefixos.
     */
    public $prefixes = array();
    /**
     *  Definição de rotas.
     */
    public $routes = array();
    /**
     *  URL atual da aplicação.
     */
    private $here = null;
    /**
     *  URL base da aplicação.
     */
    private $base = null;
    /**
     *  Controller padrão da aplicação.
     */
    public $root = null;
    /**
      *  Short description.
      */
    public $connectedDefaults = false;

    /**
     *  Define a URL base e URL atual da aplicação.
     */
    public function __construct() {
        if(is_null($this->base)):
            $this->base = dirname($_SERVER["PHP_SELF"]);
            while(in_array(basename($this->base), array("app", "webroot"))):
                $this->base = dirname($this->base);
            endwhile;
            if($this->base == DIRECTORY_SEPARATOR || $this->base == "."):
                $this->base = "/";
            endif;
        endif;
        if(isset($_SERVER["REQUEST_URI"])):
            $start = strlen($this->base);
            $this->here = self::normalize(substr($_SERVER["REQUEST_URI"], $start));
        endif;
    }
    public static function getInstance() {
        static $instance;
        if($instance === null):
            $c = __CLASS__;
            $instance = new $c();
        endif;
        
        return $instance;
    }
    public function connectDefaults() {
        $defaults = array(
            'controller' => 'home',
            'action' => 'index'
        );
        $regex = array(
            'controller' => '([a-z-_]+)',
            'action' => '([a-z-_]+)'
        );
        Mapper::connect('/', $defaults, $regex);
        Mapper::connect('/:controller', $defaults, $regex);
        Mapper::connect('/:controller/:action', $defaults, $regex);
        $this->connectedDefaults = true;
    }
    /**
     *  Getter para Mapper::here.
     *
     *  @return string Valor de Mapper:here
     */
    public static function here() {
        $self = self::getInstance();
        return $self->here;
    }
    /**
     *  Getter para Mapper::base.
     *
     *  @return string Valor de Mapper::base
     */
    public static function base() {
        $self = self::getInstance();
        return $self->base;
    }
    /**
     *  Normaliza uma URL, removendo barras duplicadas ou no final de strings e
     *  adicionando uma barra inicial quando necessário.
     *
     *  @param string $url URL a ser normalizada
     *  @return string URL normalizada
     */
    public static function normalize($url) {
        if(preg_match('/^[a-z]+:/', $url)):
            return $url;
        endif;
        $url = '/' . $url;
        while(strpos($url, '//') !== false):
            $url = str_replace('//', '/', $url);
        endwhile;
        $url = rtrim($url, '/');
        if(empty($url)):
            $url = '/';
        endif;
        return $url;
    }
    /**
     *  Define o controller padrão da aplicação.
     *
     *  @param string $controller Controller a ser definido como padrão
     *  @return true
     */
    public static function root($controller) {
        $self = self::getInstance();
        $self->root = $controller;
        return true;
    }
    /**
     *  Getter para Mapper::root
     *
     *  @return string Controller padrão da aplicação
     */
    public static function getRoot() {
        $self = self::getInstance();
        return $self->root;
    }
    /**
     *  Habilita um prefixo.
     *
     *  @param string $prefix Prefixo a ser habilitado
     *  @return true
     */
    public static function prefix($prefix) {
        $self = self::getInstance();
        if(is_array($prefix)) $prefixes = $prefix;
        else $prefixes = func_get_args();
        foreach($prefixes as $prefix):
            $self->prefixes []= $prefix;
        endforeach;
        return true;
    }
    /**
     *  Remove um prefixo da lista.
     *
     *  @param string $prefix Prefixo a ser removido
     *  @return true
     */
    public static function unsetPrefix($prefix) {
        $self = self::getInstance();
        unset($self->prefixes[$prefix]);
        return true;
    }
    /**
     *  Retorna uma lista com todos os prefixos definidos pela aplicação.
     *
     *  @return array Lista de prefixos
     */
    public static function getPrefixes() {
        $self = self::getInstance();
        return $self->prefixes;
    }
    /**
     *  Conecta uma URL a uma rota do Spaghetti.
     *
     *  @param string $url URL a ser conectada
     *  @param string $route Rota para a qual a URL será direcionada
     *  @return void
     */
    public static function connect($url, $defaults, $regex = array()) {
        $self = self::getInstance();
        $self->routes []= array(
            'url' => $url,
            'defaults' => $defaults,
            'regex' => $regex
        );
    }
    /**
     *  Desconecta uma URL de uma rota
     *
     *  @param string $url URL a ser desconectada
     *  @return true
     */
    public static function disconnect($url) {
        $self = self::getInstance();
        $url = rtrim($url, "/");
        unset($self->routes[$url]);
        return true;
    }
    /**
     *  Verifica se uma expressão regular é equivalente a uma URL.
     *
     *  @param string $check Expressão regular a ser checada
     *  @param string $url URL usada na checagem
     *  @return boolean Verdadeiro se a expressão regular conferir com a URL
     */
    public static function match($check, $url = null, &$results = null) {
        if(is_null($url)):
            $url = self::here();
        endif;
        $regex = '%^' . $check . '$%';
        
        return preg_match($regex, $url, $results) ? true : false;
    }
    /**
     *  Retorna a rota correspondente a uma URL.
     *
     *  @param string $url URL a ser convertida para uma rota
     *  @return string Rota para a URL provida
     */
    public static function getRoute($url) {
        $self = self::getInstance();
        foreach($self->routes as $map => $route):
            if(self::match($map, $url)):
                $map = "%^" . str_replace(array(":any", ":fragment", ":num"), array("(.+)", "([^\/]+)", "([0-9]+)"), $map) . "/?$%";
                $url = preg_replace($map, $route, $url);
                break;
            endif;
        endforeach;
        return self::normalize($url);
    }
    /**
     *  Faz a interpretação da URL, identificando as partes da URL.
     * 
     *  @param string $url URL a ser interpretada
     *  @return array URL interpretada
     */
    public static function parse($url = null) {
        $self = self::getInstance();
        if(!$self->connectedDefaults):
            $self->connectDefaults();
        endif;
        if(is_null($url)):
            $url = self::here();
        endif;
        $url = self::normalize($url);
        foreach($self->routes as $route):
            $check = String::insert($route['url'], $route['regex']);
            if(self::match($check, $url, $result)):
                array_shift($result);
                $parsed = array();
                $extracted = String::extract($route['url']);
                foreach($extracted as $key => $name):
                    $parsed[$name] = $result[$key];
                endforeach;
                $parsed += $route['defaults'];
                
                return $parsed;
            endif;
        endforeach;
    }
    /**
     *  Gera uma URL, levando em consideração o local atual da aplicação.
     *
     *  @param string $path Caminho relativo ou URL absoluta
     *  @param bool $full Verdadeiro para gerar uma URL completa
     *  @return string URL gerada para a aplicação
     */
    public static function url($path, $full = false) {
        if(is_array($path)):
            $here = self::parse();
            $params = $here["named"];
            $path = array_merge(array(
                "prefix" => $here["prefix"],
                "controller" => $here["controller"],
                "action" => $here["action"],
                "id" => $here["id"]
            ), $params, $path);
            $nonParams = array("prefix", "controller", "action", "id");
            $url = "";
            foreach($path as $key => $value):
                if(!in_array($key, $nonParams)):
                    $url .= "/" . "{$key}:{$value}";
                elseif(!is_null($value)):
                    if($key == "action" && $filtered = self::filterAction($value)):
                        $value = $filtered["action"];
                    endif;
                    $url .= "/" . $value;
                endif;
            endforeach;
            $url = self::normalize(self::base() . $url);
        else:
            if(preg_match("/^[a-z]+:/", $path)):
                return $path;
            elseif(substr($path, 0, 1) == "/"):
                $url = self::base() . $path;
            else:
                $url = self::base() . self::here() . "/" . $path;
            endif;
            $url = self::normalize($url);
        endif;
        return $full ? BASE_URL . $url : $url;
    }
    /**
      *  Filtra uma action, removendo prefixos.
      *
      *  @param string $action Nome da action a ser filtrada
      *  @return mixed Array contendo prefixo e action, falso caso a action não
      *                contenha prefixos
      */
    public static function filterAction($action) {
        if(strpos($action, "_") !== false):
            foreach(self::getPrefixes() as $prefix):
                if(strpos($action, $prefix) === 0):
                    return array(
                        "action" => substr($action, strlen($prefix) + 1),
                        "prefix" => $prefix
                    );
                endif;
            endforeach;
        endif;
        return false;
    }
}

?>