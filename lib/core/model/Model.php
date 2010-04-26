<?php

require 'lib/core/model/relationships/BelongsTo.php';
require 'lib/core/model/relationships/HasOne.php';
require 'lib/core/model/relationships/HasMany.php';
require 'lib/core/model/relationships/HasAndBelongsToMany.php';

class Model extends Object {
    public $belongsTo = array();
    public $hasMany = array();
    public $hasOne = array();
    public $id;
    public $schema = array();
    public $table;
    public $primaryKey;
    public $displayField;
    public $connection;
    public $order;
    public $limit;
    public $perPage = 20;
    public $validates = array();
    public $errors = array();
    public $associations = array('belongsTo', 'hasOne', 'hasMany', 'hasAndBelongsToMany');
    public $pagination = array();
    protected $conn;

    public function __construct() {
        if(!$this->connection):
            $this->connection = Config::read('App.environment');
        endif;
        
        if(is_null($this->table)):
            $database = Connection::getConfig($this->connection);
            $this->table = $database['prefix'] . Inflector::underscore(get_class($this));
        endif;
        
        $this->setSource($this->table);
        
        ClassRegistry::addObject(get_class($this), $this);
        
        $this->createRelations();
    }
    public function __call($method, $condition) {
        if(preg_match('/(all|first)By([\w]+)/', $method, $match)):
            $field = Inflector::underscore($match[2]);
            $params = array(
                'conditions' => array(
                    $field = $condition[0]
                )
            );
            if(isset($condition[1])):
                $params += $condition[1];
            endif;
            return $this->{$match[1]}($params);
        else:
            trigger_error('Call to undefined method Model::' . $method . '()', E_USER_ERROR);
            return false;
        endif;
    }
    public function connection() {
        if(!$this->conn):
            $this->conn = Connection::get($this->connection);
        endif;
        return $this->conn;
    }
    public function setSource($table) {
        $db = $this->connection();
        if($table):
            $this->table = $table;
            $sources = $db->listSources();
            if(!in_array($this->table, $sources)):
                $this->error('missingTable', array('model' => get_class($this), 'table' => $this->table));
                return false;
            endif;
            if(empty($this->schema)):
                $this->describe();
            endif;
        endif;
        return true;
    }
    public function describe() {
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
    public function createRelations() {
        foreach($this->associations as $type):
            $associations =& $this->{$type};
            foreach($associations as $key => $properties):
                // normalizes the association
                if(is_numeric($key)):
                    unset($associations[$key]);
                    if(is_array($properties)):
                        $associations[$key = $properties['className']] = $properties;
                    else:
                        $associations[$key = $properties] = array('className' => $properties);
                    endif;
                elseif(!isset($properties['className'])):
                    $associations[$key]['className'] = $key;
                endif;
                
                // creates the actual relationship
                $relationship = Inflector::camelize($type);
                $associations[$key] = new $relationship($key, $associations[$key]);
            endforeach;
        endforeach;
        
        return true;
    }
    public function query($query) {
        return $this->connection()->query($query);
    }
    public function fetch($query) {
        return $this->connection()->fetchAll($query);
    }
    public function begin() {
        return $this->connection()->begin();
    }
    public function commit() {
        return $this->connection()->commit();
    }
    public function rollback() {
        return $this->connection()->rollback();
    }
    public function all($params = array()) {
        $db = $this->connection();
        $params += array(
            'table' => $this->table,
            'fields' => array_keys($this->schema),
            'order' => $this->order,
            'limit' => $this->limit
        );
        $results = $db->read($params);
        
        return $results;
    }
    public function first($params = array()) {
        $params += array(
            'limit' => 1
        );
        $results = $this->all($params);
        
        return empty($results) ? array() : $results[0];
    }
    public function count($params = array()) {
        $db = $this->connection();
        $params += array(
            'fields' => '*',
            'table' => $this->table
        );
        
        return $db->count($params);
    }
    public function paginate($params = array()) {
        $params += array(
            'perPage' => $this->perPage,
            'page' => 1
        );
        $page = !$params['page'] ? 1 : $params['page'];
        $offset = ($page - 1) * $params['perPage'];
        $params['limit'] = $offset . ',' . $params['perPage'];

        $totalRecords = $this->count($params);
        $this->pagination = array(
            'totalRecords' => $totalRecords,
            'totalPages' => ceil($totalRecords / $params['perPage']),
            'perPage' => $params['perPage'],
            'offset' => $offset,
            'page' => $page
        );

        return $this->all($params);
    }
    public function toList($params = array()) {
        $params += array(
            'key' => $this->primaryKey,
            'displayField' => $this->displayField
        );
        $all = $this->all($params);
        $results = array();
        foreach($all as $result):
            $results[$result[$params['key']]] = $result[$params['displayField']];
        endforeach;
        
        return $results;
    }
    public function exists($id) {
        $params = array(
            'conditions' => array(
                $this->primaryKey => $id
            )
        );
        $row = $this->first($params);

        return !empty($row);
    }
    public function insert($data) {
        $db = $this->connection();
        $params = array(
            'values' => $data,
            'table' => $this->table
        );
        return $db->create($params);
    }
    public function update($params, $data) {
        $db = $this->connection();
        $params += array(
            'values' => $data,
            'table' => $this->table
        );
        
        return $db->update($params);
    }
    public function save($data) {
        if(isset($data[$this->primaryKey]) && !is_null($data[$this->primaryKey])):
            $this->id = $data[$this->primaryKey];
        elseif(!is_null($this->id)):
            $data[$this->primaryKey] = $this->id;
        endif;
        foreach($data as $field => $value):
            if(!isset($this->schema[$field])):
                unset($data[$field]);
            endif;
        endforeach;
        $date = date('Y-m-d H:i:s');
        if(isset($this->schema['modified']) && !isset($data['modified'])):
            $data['modified'] = $date;
        endif;
        $exists = $this->exists($this->id);
        if(!$exists && isset($this->schema['created']) && !isset($data['created'])):
            $data['created'] = $date;
        endif;
        if(!($data = $this->beforeSave($data))) return false;
        if(!is_null($this->id) && $exists):
            $save = $this->update(array(
                'conditions' => array(
                    $this->primaryKey => $this->id
                ),
                'limit' => 1
            ), $data);
            $created = false;
        else:
            $save = $this->insert($data);
            $created = true;
            $this->id = $this->getInsertId();
        endif;
        $this->afterSave($created);
        return $save;
    }
    public function validate($data) {
        $this->errors = array();
        $defaults = array(
            'required' => false,
            'allowEmpty' => false,
            'message' => null
        );
        foreach($this->validates as $field => $rules):
            if(!is_array($rules) || (is_array($rules) && isset($rules['rule']))):
                $rules = array($rules);
            endif;
            foreach($rules as $rule):
                if(!is_array($rule)):
                    $rule = array('rule' => $rule);
                endif;
                $rule += $defaults;
                if($rule['allowEmpty'] && empty($data[$field])):
                    continue;
                endif;
                $required = !isset($data[$field]) && $rule['required'];
                if($required):
                    $this->errors[$field] = is_null($rule['message']) ? $rule['rule'] : $rule['message'];
                elseif(isset($data[$field])):
                    if(!$this->callValidationMethod($rule['rule'], $data[$field])):
                        $message = is_null($rule['message']) ? $rule['rule'] : $rule['message'];
                        $this->errors[$field] = $message;
                        break;
                    endif;
                endif;
            endforeach;
        endforeach;
        return empty($this->errors);
    }
    public function callValidationMethod($params, $value) {
        $method = is_array($params) ? $params[0] : $params;
        $class = method_exists($this, $method) ? $this : 'Validation';
        if(is_array($params)):
            $params[0] = $value;
            return call_user_func_array(array($class, $method), $params);
        else:
            if($class == 'Validation'):
                return Validation::$params($value);
            else:
                return $this->$params($value);
            endif;
        endif;
    }
    public function beforeSave($data) {
        return $data;
    }
    public function afterSave($created) {
        return $created;
    }
    public function delete($id) {
        $params = array(
            'conditions' => array(
                $this->primaryKey => $id
            ),
            'limit' => 1
        );
        if($this->exists($id) && $this->deleteAll($params)):
            return true;
        endif;
        return false;
    }
    public function deleteAll($params = array()) {
        $db = $this->connection();
        $params += array(
            'table' => $this->table,
            'order' => $this->order,
            'limit' => $this->limit
        );
        return $db->delete($params);
    }
    public function getInsertId() {
        return $this->connection()->insertId();
    }
    public function getAffectedRows() {
        return $this->connection()->affectedRows();
    }
    public function escape($value) {
        return $this->connection()->escape($value);
    }
}