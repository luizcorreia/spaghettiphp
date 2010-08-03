<?php

require 'lib/core/model/Exceptions.php';
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

    protected $behaviors = array();

    protected $displayField;

    protected $order;
    protected $limit;

    protected $perPage = 20;
    protected $pagination = array();

    protected $validates = array();
    protected $errors = array();

    public $connection = 'default';

    protected static $instances = array();

    public function __construct() {
        if(is_null($this->connection)):
            $this->connection = Config::read('App.environment');
        endif;
        
       if(is_null($this->table)):
            $database = Connection::getConfig($this->connection);
            $this->table = $database['prefix'] . Inflector::underscore(get_class($this));
        endif;
        
        // @todo move to the first query
        $this->setSource($this->table);
        
        $this->createRelations();
        Model::$instances[get_class($this)] = $this;        

        $this->loadBehaviors($this->behaviors);
    }
    public function __call($method, $args) {
        $regex = '/(?<method>first|all|get)(?:By)?(?<complement>[a-z]+)/i';
        if(preg_match($regex, $method, $output)):
            $complement = Inflector::underscore($output['complement']);
            $conditions = explode('_and_', $complement);
            $params = array();

            if($output['method'] == 'get'):
                if(is_array($args[0])):
                    $params['conditions'] = $args[0];
                elseif(is_numeric($args[0])):
                    $params['conditions']['id'] = $args[0];
                endif;

                $params['fields'][] = $conditions[0];
                $result =  $this->first($params);

                return $result[$conditions[0]];
            else:
                $params['conditions'] = array_combine($conditions, $args);

                return $this->$output['method']($params);
            endif;

        else:
            //trigger_error('Call to undefined method Model::' . $method . '()', E_USER_ERROR);
            return false;
        endif;
    }
    public static function load($name) {
        if(!array_key_exists($name, Model::$instances)):
            if(!class_exists($name) && Filesystem::exists('app/models/' . Inflector::underscore($name) . '.php')):
                require_once 'app/models/' . Inflector::underscore($name) . '.php';
            endif;
            if(class_exists($name)):
                Model::$instances[$name] = new $name();
                // @todo remove this
                Model::$instances[$name]->createLinks();
            else:
                throw new MissingModelException(array(
                    'model' => $name
                ));
            endif;
        endif;

        return Model::$instances[$name];
    }
    public function connection() {
        return Connection::get($this->connection);
    }
    /**
     * @todo refactor
     */
    public function setSource($table) {
        if($table):
            $db = $this->connection();
            $this->table = $table;
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
        endif;
        return true;
    }
    /**
     * @todo refactor
     */
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
    public function loadModel($model) {
        return $this->{$model} = Model::load($model);
    }
    public function createRelations() {
        foreach($this->associations as $type):
            $associations = array();
            $relationship = Inflector::camelize($type);
            foreach($this->{$type} as $key => $properties):
                if(is_array($properties)):
                    $properties['name'] = $key;
                else:
                    $key = $properties;
                endif;
                $associations[$key] = new $relationship($properties);
            endforeach;
            $this->{$type} = $associations;
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
        return $this->{$behavior} = new $behavior($this, $options);
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
        $params = array_merge($params, array(
            'fields' => '*',
            'table' => $this->table,
            'limit' => null
        ));
        return $db->count($params);
    }
    public function paginate($params = array()) {
        $params += array(
            'perPage' => $this->perPage,
            'page' => 1
        );
        $page = !$params['page'] ? 1 : $params['page'];
        $offset = ($page - 1) * $params['perPage'];
        // @todo do we really need limits and offsets together here?
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
    /**
     * @todo refactor. check for fields
     */
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
    public function save($data) {
        if(!is_null($this->id)):
            $data[$this->primaryKey] = $this->id;
        endif;

        // apply modified timestamp
        $date = date('Y-m-d H:i:s');
        if(!array_key_exists('modified', $data)):
            $data['modified'] = $date;
        endif;

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

        // fire afterSave action
        $this->fireAction('afterSave');

        return $save;
    }
    /**
     * @todo refactor
     */
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
    public function delete($id) {
        $params = array(
            'conditions' => array(
                $this->primaryKey => $id
            ),
            'limit' => 1
        );
        if($this->exists(array($this->primaryKey => $id)) && $this->deleteAll($params)):
            if($dependent):
                $this->deleteDependent($id);
            endif;
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
