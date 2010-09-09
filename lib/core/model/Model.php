<?php

require 'lib/core/model/Connection.php';
require 'lib/core/model/Relationship.php';
require 'lib/core/model/Behavior.php';

class Model extends Hookable {
    public $id;
    protected $primaryKey;
    protected $schema = array();
    protected $table;

    protected $associations = array('belongsTo', 'hasOne', 'hasMany', 'hasAndBelongsToMany');
    protected $belongsTo = array();
    protected $hasAndBelongsToMany = array();
    protected $hasMany = array();
    protected $hasOne = array();
    protected $__relationships = array();

    protected $behaviors = array();
    protected $__behaviors = array();

    protected $displayField;

    protected $order;
    protected $limit;

    protected $perPage = 20;
    protected $pagination = array();

    protected $validates = array();
    protected $errors = array();

    protected $connection = 'default';
    protected $connected = false;

    protected $data = array();
    
    protected $beforeSave = array();
    protected $beforeCreate = array();
    protected $beforeUpdate = array();
    protected $beforeDelete = array();
    protected $beforeValidate = array();

    protected $afterSave = array();
    protected $afterCreate = array();
    protected $afterUpdate = array();
    protected $afterDelete = array();
    protected $afterValidate = array();

    protected static $instances = array();

    public function __construct($data = null, $new_record = true) {
        $this->initialize($data, $new_record);
    }
    protected function initialize($data, $new_record) {
        $this->connection();
        if(!empty($data)):
            $this->data = array_intersect_key($data, $this->schema);
        endif;
    }
    public function __get($field) {
        if(array_key_exists($field, $this->__behaviors)):
            return $this->__behaviors[$field];
        elseif(array_key_exists($field, $this->schema())):
            return array_key_exists($field, $this->data) ? $this->data[$field] : null;
        else:
            throw new RuntimeException(get_class($this) . '::$' . $field . ' does not exist.');
        endif;
    }
    public function __set($field, $value) {
        if(array_key_exists($field, $this->schema())):
            $this->data[$field] = $value;
        else:
            $this->{$field} = $value;
        endif;
    }
    public function __call($method, $args) {
        $regex = '/(?P<method>first|all)By(?P<fields>[\w]+)/';
        if(preg_match($regex, $method, $output)):
            $fields = Inflector::underscore($output['fields']);
            $fields = explode('_and_', $fields);

            $conditions = array_slice($args, 0, count($fields));

            $params = array_slice($args, count($fields));
            $params['conditions'] = array_combine($fields, $conditions);

            return $this->$output['method']($params);
        endif;

        throw new BadMethodCallException(get_class($this) . '::' . $method . ' does not exist.');
    }
    public static function load($name) {
        if(!array_key_exists($name, Model::$instances)):
            if(!class_exists($name) && Filesystem::exists('app/models/' . Inflector::underscore($name) . '.php')):
                require_once 'app/models/' . Inflector::underscore($name) . '.php';
            endif;
            if(class_exists($name)):
                Model::$instances[$name] = new $name();
                Model::$instances[$name]->connection();
                Model::$instances[$name]->createRelations();
            else:
                $message = 'The model <code>' . $name . '</code> was not found.';
                throw new InternalErrorException('Missing Model', 0, $message);
            endif;
        endif;

        return Model::$instances[$name];
    }
    public function connection() {
        if(!$this->connected):
            $this->connected = true;

            if(is_null($this->connection)):
                $this->connection = Config::read('App.environment');
            endif;
            
            if(is_null($this->table)):
                $this->table = Inflector::underscore(get_class($this));
            endif;
            $database = Connection::getConfig($this->connection);
            $this->table = $database['prefix'] . $this->table;
            
            $this->loadBehaviors($this->behaviors);

            $this->schema();
        endif;
        
        return Connection::get($this->connection);
    }
    public function schema() {
        if(empty($this->schema) && $this->table):
            $db = $this->connection();
            $this->schema = $db->describe($this->table);
            if(is_null($this->primaryKey)):
                $this->primaryKey = $db->primaryKeyFor($this->table);
            endif;
        endif;

        return $this->schema;
    }
    public function createRelations() {
        foreach($this->associations as $type):
            $relationship = Inflector::camelize($type);
            
            foreach($this->{$type} as $key => $properties):
                if(is_array($properties)):
                    $properties['name'] = $key;
                else:
                    $key = $properties;
                endif;
                
                $this->__relationships[$key] = new $relationship($properties);
            endforeach;
        endforeach;
    }
    protected function loadBehaviors($behaviors) {
        foreach($this->behaviors as $key => $behavior):
            $options = array();
            if(!is_numeric($key)):
                $options = $behavior;
                $behavior = $key;
            endif;
            
            $this->loadBehavior($behavior, $options);
        endforeach;
    }
    protected function loadBehavior($behavior, $options = array()) {
        $behavior = Inflector::camelize($behavior);
        Behavior::load($behavior);
        
        return $this->__behaviors[$behavior] = new $behavior($this, $options);
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
        $params += array(
            'fields' => '*',
            'table' => $this->table,
            'order' => $this->order,
            'limit' => $this->limit
        );
        $results = $this->connection()->read($params);

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
        $params = array_merge($params, array(
            'table' => $this->table,
            'offset' => null,
            'limit' => null
        ));

        return $this->connection()->count($params);
    }
    public function paginate($params = array()) {
        $params += array(
            'perPage' => $this->perPage,
            'page' => 1
        );

        $params['offset'] = ($params['page'] - 1) * $params['perPage'];
        $params['limit'] = $params['perPage'];

        $totalRecords = $this->count($params);

        $this->pagination = array(
            'totalRecords' => $totalRecords,
            'totalPages' => ceil($totalRecords / $params['perPage']),
            'perPage' => $params['perPage'],
            'offset' => $offset,
            'page' => $params['page']
        );

        return $this->all($params);
    }
    public function toList($params = array()) {
        $params += array(
            'key' => $this->primaryKey,
            'displayField' => $this->displayField,
            'table' => $this->table,
            'order' => $this->order,
            'limit' => $this->limit
        );
        $params['fields'] = array($params['key'], $params['displayField']);

        $all = $this->connection()->read($params);

        $results = array();
        foreach($all as $result):
            $results[$result[$params['key']]] = $result[$params['displayField']];
        endforeach;

        return $results;
    }
    public function exists($conditions) {
        $params = array(
            'conditions' => $conditions
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
    /**
     * @todo refactor
     */
    public function save($data = array()) {
        if(empty($data)):
            $data = $this->data;
        endif;

        // apply modified timestamp
        $date = date('Y-m-d H:i:s');
        if(!array_key_exists('modified', $data)):
            $data['modified'] = $date;
        endif;

        $db = $this->connection(); // yes, this is a hack
        // verify if the record exists
        $exists = $this->exists(array(
            $this->primaryKey => $this->id
        ));

        // apply created timestamp
        if(!$exists && !array_key_exists('created', $data)):
            $data['created'] = $date;
        endif;

        // apply beforeSave filter
        $data = $this->fireFilter('beforeSave', $data);
        if(!$data):
            return false;
        endif;

        // filter fields that are not in the schema
        $data = array_intersect_key($data, $this->schema);

        // update a record if it already exists...
        if($exists):
            $save = $this->update(array(
                'conditions' => array(
                    $this->primaryKey => $this->id
                ),
                'limit' => 1
            ), $data);
        // or insert a new one if it doesn't
        else:
            $save = $this->insert($data);
            $this->id = $this->getInsertId();
        endif;
        
        $this->data = $this->first(array(
            'conditions' => array(
                $this->primaryKey => $this->id
            )
        ));
        
        // fire afterSave action
        $this->fireAction('afterSave');

        return $save;
    }
    /**
     * @todo refactor
     */
    public function validate($data = null) {
        if(is_null($data)):
            $data = $this->data;
        endif;
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
    /**
     * @todo refactor
     */
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
    public function hasError($field) {
        return array_key_exists($field, $this->errors);
    }
    public function delete($id) {
        $params = array(
            'conditions' => array($this->primaryKey => $id),
            'limit' => 1
        );
        
        if($this->exists(array($this->primaryKey => $id))):
            return $this->deleteAll($params);
        endif;
        
        return false;
    }
    public function deleteAll($params = array()) {
        $params += array(
            'table' => $this->table,
            'order' => $this->order,
            'limit' => $this->limit
        );

        return $this->connection()->delete($params);
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
    public function create($data = array()) {
        $self = get_class($this);
        return new $self($data);
    }
}