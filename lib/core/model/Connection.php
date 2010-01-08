<?php
/**
 *  Connection é a classe que cuida das conexões com banco de dados no Spaghetti,
 *  encontrando e carregando datasources de acordo com a configuração desejada.
 *
 *  @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 *  @copyright Copyright 2008-2009, Spaghetti* Framework (http://spaghettiphp.org/)
 *
 */

class Connection extends Object {
    /**
     *  Configurações de banco de dados da aplicação.
     */
    private $config = array();
    /**
     *  Datasources já instanciados.
     */
    private $datasources = array();
    
    /**
     *  Lendo arquivos de configuração do banco de dados.
     */
    public function __construct() {
        $this->config = Config::read("database");
    }
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
        $environment = is_null($environment) ? Config::read('App.environment') : $environment;
        
        if(isset($self->config[$environment])):
            $config = $self->config[$environment];
        else:
            throw new Exception('Can\'t find database configuration. Check /app/config/database.php');
        endif;

        $datasource = Inflector::camelize($config['driver']) . 'Datasource';
        
        if(isset($self->datasources[$environment])):
            return $self->datasources[$environment];
        elseif(self::loadDatasource($datasource)):
            $self->datasources[$environment] = new $datasource($config);
            return $self->datasources[$environment];
        else:
            throw new Exception('Can\'t find ' . $datasource . ' datasource');
        endif;
    }
    /**
     *  Carrega um datasource.
     *
     *  @param string $datasource Nome do datasource
     *  @return boolean Verdadeiro se o datasource existir e for carregado
     */
    public static function loadDatasource($datasource = null) {
        if(!class_exists($datasource)):
            import('core.model.datasources.' . $datasource);
        endif;
        return class_exists($datasource);
   }
}

?>