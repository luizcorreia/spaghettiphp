<?php
/**
 *  Short description.
 *
 *  @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 *  @copyright Copyright 2008-2010, Spaghetti* Framework (http://spaghettiphp.org/)
 */

class Connection extends Object {
    /**
     *  Configurações de banco de dados da aplicação.
     */
    protected $config = array();
    /**
     *  Datasources já instanciados.
     */
    protected $datasources = array();
    
    /**
     *  Lendo arquivos de configuração do banco de dados.
     */
    public function __construct() {
        // @todo change config var for databases
        $this->config = Config::read("database");
    }
    /**
      *  Short description.
      *
      *  @return object
      */
    public static function &instance() {
        static $instance;
        if($instance === null):
            $c = __CLASS__;
            $instance = new $c();
        endif;
        
        return $instance;
    }
    /**
     *  Cria uma instância de um datasource ou retorna outra instância existente.
     *
     *  @param string $environment Configuração de ambiente a ser usada
     *  @return object Instância do datasource
     */
    public static function &datasource($environment = null) {
        $self = self::instance();
        if(is_null($environment)):
            $environment =  Config::read('App.environment');
        endif;
        if(isset($self->config[$environment])):
            $config = $self->config[$environment];
        else:
            throw new Exception('Can\'t find database configuration. Check /app/config/database.php');
        endif;
        $datasource = Inflector::camelize($config['driver']) . 'Datasource';
        if(!class_exists($datasource)):
            self::loadDatasource($datasource);
        endif;
        if(!isset($self->datasources[$environment])):
            $self->datasources[$environment] = new $datasource($config);
        endif;
        
        return $self->datasources[$environment];
    }
    /**
     *  Carrega um datasource.
     *
     *  @param string $datasource Nome do datasource
     *  @return boolean Verdadeiro se o datasource existir e for carregado
     */
    public static function loadDatasource($datasource) {
        import('core.model.datasources.' . $datasource);
        if(!class_exists($datasource)):
            throw new Exception('Can\'t find ' . $datasource . ' datasource');
        endif;
        
        return true;
   }
}