<?php
/**
 *  Mapper é o responsável por cuidar de URLs e roteamento dentro do Spaghetti*.
 *
 *  @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 *  @copyright Copyright 2008-2010, Spaghetti* Framework (http://spaghettiphp.org/)
 */

class Mapper extends Object {
    /**
     *  Definição de rotas.
     */
    protected $routes = array();
    /**
     *  URL atual da aplicação.
     */
    protected $here = null;
    /**
     *  URL base da aplicação.
     */
    protected $base = null;

    /**
     *  Define a URL base e URL atual da aplicação.
     */
    public function __construct() {
        if(is_null($this->base)):
            if(Config::read('App.rewriteUrl')):
                $this->base = dirname($_SERVER["PHP_SELF"]);
            else:
                $this->base = $_SERVER["SCRIPT_NAME"];
            endif;
            if($this->base == DIRECTORY_SEPARATOR || $this->base == "."):
                $this->base = "/";
            endif;
        endif;
        if(isset($_SERVER["REQUEST_URI"])):
            $start = strlen($this->base);
            $this->here = self::normalize(substr($_SERVER["REQUEST_URI"], $start));
        endif;
    }
    public static function &getInstance() {
        static $instance;
        if($instance === null):
            $c = __CLASS__;
            $instance = new $c();
        endif;
        
        return $instance;
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
     *  Conecta uma URL a uma rota do Spaghetti.
     *
     *  @param string $url URL a ser conectada
     *  @param string $route Rota para a qual a URL será direcionada
     *  @return void
     */
    public static function connect($url, $defaults = array(), $regex = array()) {
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
      *  Short description.
      *
      *  @return array
      */
    public static function getRoute($url) {
        $self = self::getInstance();
        foreach($self->routes as $route):
            $check = String::insert($route['url'], $route['regex']);
            if(self::match($check, $url, $result)):
                array_shift($result);
                $route['result'] = $result;

                return $route;
            endif;
        endforeach;
    }
    /**
     *  Faz a interpretação da URL, identificando as partes da URL.
     * 
     *  @param string $url URL a ser interpretada
     *  @return array URL interpretada
     */
    public static function parse($url = null) {
        if(is_null($url)):
            $url = self::here();
        endif;
        $route = self::getRoute(self::normalize($url));
        $parsed = array();
        $extracted = String::extract($route['url']);
        foreach($extracted as $key => $name):
            $parsed[$name] = $route['result'][$key];
        endforeach;
        $parsed += $route['defaults'];
        
        return $parsed;
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