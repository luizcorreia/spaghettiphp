<?php

class Table {
    protected $primaryKey;
    protected $schema;
    protected $table;
    protected $model;
    protected $connection;
    protected static $cache = array();

    public function __construct($connection, $model) {
        $this->connection = $connection;
        $this->model = $model;
    }

    public static function load($model) {
        $connection = $model::connectionName();
        $name = $connection . '.' . $model;
        
        if(!array_key_exists($name, self::$cache)) {
            self::$cache[$name] = new self($connection, $model);
        }
        
        return self::$cache[$name];
    }

    public function connection() {
        return Connection::get($this->connection);
    }

    public function name() {
        if(is_null($this->table)) {
            $model = $this->model;
            $this->table = $model::tableName();

            if(is_null($this->table)) {
                $database = Connection::config($this->connection);
                $this->table = $database['prefix'] . Inflector::underscore($model);
            }
        }
        
        return $this->table;
    }

    public function schema() {
        if($this->name() && is_null($this->schema)) {
            $db = $this->connection();
            $sources = $db->listSources();
            if(!in_array($this->table, $sources)):
                throw new MissingTableException(array(
                    'table' => $this->table
                ));
                return false;
            endif;
            if(empty($this->schema)):
                $this->describe();
            endif;
        }
        
        return $this->schema;
    }

    public function primaryKey() {
        if($this->name() && $this->schema()) {
            return $this->primaryKey;
        }
    }

    protected function describe() {
        $db = $this->connection();
        $schema = $db->describe($this->table);
        if(is_null($this->primaryKey)):
            foreach($schema as $field => $describe):
                if($describe['key'] == 'PRI'):
                    $this->primaryKey = $field;
                    break;
                endif;
            endforeach;
        endif;
        return $this->schema = $schema;
    }
}