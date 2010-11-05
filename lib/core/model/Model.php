<?php

require 'lib/core/model/Connection.php';
require 'lib/core/model/Table.php';
require 'lib/core/model/Exceptions.php';
require 'lib/core/model/Behavior.php';

class Model {
    protected static $table;
    protected static $connection = 'default';
    
    protected static $belongsTo;
    protected static $hasOne;
    protected static $hasMany;
    protected static $hasAndBelongsToMany;
    
    protected static $defaultScope = array();
    protected static $displayField = null;
    protected static $perPage = 20;
    
    protected static $validates;
    protected $errors;
    
    protected $data;
    
    protected function __construct($data, $guard = true, $new = true) {
        $this->data = $data;
    }
    
    public static function load($name) {
        $filename = 'app/models/' . Inflector::underscore($name) . '.php';
        if(!class_exists($name) && Filesystem::exists($filename)) {
            require_once $filename;
        }

        if(class_exists($name)) {
            // Model::$instances[$name] = new $name();
            // Model::$instances[$name]->createLinks();
        }
        else {
            throw new MissingModelException(array(
                'model' => $name
            ));
        }
    }
    
    public static function tableName() {
        $self = get_called_class();
        return $self::$table;
    }

    public static function connectionName() {
        $self = get_called_class();
        return $self::$connection;
    }
    
    protected static function table() {
        return Table::load(get_called_class());
    }

    protected static function connection() {
        $self = get_called_class();
        return $self::table()->connection();
    }

    protected static function scope($scope, $params, $defaults = array()) {
        if(is_array($scope)) {
            $params = $scope;
            $scope = 'default';
        }
        
        if(is_null($scope)) {
            $scope = 'default';
        }
        
        if($scope !== false) {
            $self = get_called_class();
            $scope_name = $scope . 'Scope';
            $scope = $self::$$scope_name;
        }
        else {
            $scope = array();
        }
        
        return array_merge($defaults, $scope, $params);
    }

    public static function query($sql, $values = array()) {
        $self = get_called_class();
        return $self::connection()->query($sql);
    }
    
    public static function fetch($sql, $values = array()) {
        $self = get_called_class();
        return $self::connection()->fetchAll($sql);
    }
    
    public static function begin() {
        $self = get_called_class();
        return $self::connection()->begin();
    }
    
    public static function commit() {
        $self = get_called_class();
        return $self::connection()->commit();
    }
    
    public static function rollback() {
        $self = get_called_class();
        return $self::connection()->rollback();
    }
    
    public static function escape($value) {
        $self = get_called_class();
        return $self::connection()->escape($value);
    }
    
    public static function insertId() {
        $self = get_called_class();
        return $self::connection()->insertId();
    }
    
    public static function affectedRows() {
        $self = get_called_class();
        return $self::connection()->affectedRows();
    }

    public static function create($data) {
        $self = get_called_class();
        return new $self($data);
    }
    
    public static function all($scope = null, $params = array()) {
        $self = get_called_class();
        $table = $self::table();
        $defaults = array( 'table' => $table->name() );
        $params = $self::scope($scope, $params, $defaults);

        $query = $table->connection()->read($params);

        $results = array();
        while($result = $query->fetch()) {
            $results []= new $self($result, false, false);
        }

        return $results;
    }
    
    public static function first($scope = null, $params = array()) {
        $self = get_called_class();
        $params['limit'] = 1;
        $results = $self::all($scope, $params);

        return empty($results) ? null : $results[0];
    }
    
    public static function find($id) {
        $self = get_called_class();
        $pk = $self::table()->primaryKey();
        $params = array(
            'conditions' => array(
                $pk => $id
            )
        );
        
        return $self::first($params);
    }
    
    public static function count($scope = null, $params = array()) {
        $self = get_called_class();
        $table = $self::table();
        $defaults = array( 'table' => $table->name() );
        $params = $self::scope($scope, $params, $defaults);
        unset($params['offset'], $params['limit']);
        
        return $self::connection()->count($params);
    }
    
    public static function paginate($scope = null, $params = array()) {
        $self = get_called_class();
        $count = $self::count($scope, $params);

        $defaults = array(
            'perPage' => $self::$perPage,
            'page' => 1
        );
        $params = $self::scope($scope, $params, $defaults);

        $params['offset'] = ($params['page'] - 1) * $params['perPage'];
        $params['limit'] = $params['perPage'];

        // $this->pagination = array(
        //     'totalRecords' => $count,
        //     'totalPages' => ceil($count / $params['perPage']),
        //     'perPage' => $params['perPage'],
        //     'offset' => $params['offset'],
        //     'page' => $params['page']
        // );

        return $self::all(false, $params);
    }
    
    public static function toList($scope = null, $params = array()) {
        $self = get_called_class();
        $table = $self::table();
        $defaults = array(
            'key' => $table->primaryKey(),
            'displayField' => $self::$displayField,
            'table' => $table->name()
        );
        $params = $self::scope($scope, $params, $defaults);
        
        if(!array_key_exists('fields', $params)) {
            $params['fields'] = array_merge(
                (array) $params['key'],
                (array) $params['displayField']
            );
        }

        $all = $self::connection()->read($params);

        $results = array();
        while($result = $all->fetch()) {
            if(is_array($params['displayField'])) {
                $keys = array_flip($params['displayField']);
                $value = array_intersect_key($result, $keys);
            }
            else {
                $value = $result[$params['displayField']];
            }
            
            $results[$result[$params['key']]] = $value;
        }

        return $results;
    }
    
    public static function exists($conditions) {
        $self = get_called_class();
        
        if(is_numeric($conditions)) {
            $pk = $self::table()->primaryKey();
            $conditions = array(
                $pk => $conditions
            );
        }
        
        return (bool) $self::count(array(
            'conditions' => $conditions
        ));
    }
    
    public static function update($params, $data) {
        $self = get_called_class();
        $table = $self::table();
        $params += array(
            'values' => $data,
            'table' => $table->name()
        );

        return $table->connection()->update($params);
    }
    
    public static function insert($data) {
        $self = get_called_class();
        $table = $self::table();
        $params = array(
            'values' => $data,
            'table' => $table->name()
        );

        return $table->connection()->create($params);
    }

    public static function deleteAll($scope = null, $params = array()) {
        $self = get_called_class();
        $table = $self::table();
        $defaults = array( 'table' => $table->name() );
        $params = $self::scope($scope, $params, $defaults);
        
        return $self::connection()->delete($params);
    }
    
    public function save() {
        return true;
    }
    
    public function delete() {
        $self = get_class($this);
        $pk = $self::table()->primaryKey();
        $params = array(
            'conditions' => array(
                $pk => $id
            ),
            'limit' => 1
        );
        
        if($self::exists($id)) {
            return $self::deleteAll($params);
        }

        return false;
    }
    
    public function isValid() {
        return true;
    }
}

class OldModel extends Hookable {
    public $id;

    public $associations = array(
        'hasMany' => array('primaryKey', 'foreignKey', 'limit', 'order'),
        'belongsTo' => array('primaryKey', 'foreignKey'),
        'hasOne' => array('primaryKey', 'foreignKey')
    );
    protected $belongsTo = array();
    protected $hasMany = array();
    protected $hasOne = array();

    protected $behaviors = array();

    protected $displayField;
    protected $table;
    protected $connection = 'default';

    protected $defaultScope = array(
        'recursion' => 0,
        'orm' => false
    );

    protected $perPage = 20;
    public $pagination = array();

    protected $validates = array();
    protected $errors = array();

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

    protected $_models = array();
    protected $_behaviors = array();

    protected static $instances = array();

    public function __construct($data = null) {
        if(!is_null($data)) {
            $this->id = $data['id'];
            $this->data = $data;
        }
        $this->loadBehaviors($this->behaviors);
    }
    
    public function __call($method, $args) {
        $regex = '/(?P<method>first|all)By(?P<fields>[\w]+)/';
        if(preg_match($regex, $method, $output)) {
            $fields = Inflector::underscore($output['fields']);
            $fields = explode('_and_', $fields);

            $conditions = array_slice($args, 0, count($fields));

            $params = array_slice($args, count($fields));
            $params['conditions'] = array_combine($fields, $conditions);

            return $this->$output['method']($params);
        }

        throw new BadMethodCallException(get_class($this) . '::' . $method . ' does not exist.');
    }

    public function __set($name, $value) {
        // @todo shouldn't fail silently
        $this->data[$name] = $value;
    }
    
    public function __get($name) {
        $attrs = array('data', '_models', '_behaviors');
        
        foreach($attrs as $attr) {
            if(array_key_exists($name, $this->{$attr})) {
                return $this->{$attr}[$name];
            }
        }
        
        throw new RuntimeException(get_class($this) . '->' . $name . ' does not exist.');
    }
    
    public static function load($name) {
        if(!array_key_exists($name, Model::$instances)) {
            $filename = 'app/models/' . Inflector::underscore($name) . '.php';
            if(!class_exists($name) && Filesystem::exists($filename)) {
                require_once $filename;
            }

            if(class_exists($name)) {
                Model::$instances[$name] = new $name();
                Model::$instances[$name]->createLinks();
            }
            else {
                throw new MissingModelException(array(
                    'model' => $name
                ));
            }
        }

        return Model::$instances[$name];
    }

    public function getConnection() {
        return $this->connection;
    }

    public function connection() {
        return Table::load($this)->connection();
    }

    protected function table() {
        return Table::load($this)->name();
    }

    public function getTable() {
        return $this->table;
    }
    
    public function schema() {
        return Table::load($this)->schema();
    }

    public function primaryKey() {
        return Table::load($this)->primaryKey();
    }

    public function createLinks() {
        foreach(array_keys($this->associations) as $type):
            $associations =& $this->{$type};
            foreach($associations as $key => $properties):
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

                $model = $associations[$key]['className'];
                if(!array_key_exists($model, $this->_models)) {
                    $this->_models[$model] = Model::load($model);
                }

                $associations[$key] = $this->generateAssociation($type, $associations[$key]);
            endforeach;
        endforeach;
    }
    
    public function generateAssociation($type, $association) {
        foreach($this->associations[$type] as $key):
            if(!isset($association[$key])):
                $data = null;
                switch($key):
                    case 'primaryKey':
                        $data = $this->primaryKey();
                        break;
                    case 'foreignKey':
                        if($type == 'belongsTo'):
                            $data = Inflector::underscore($association['className'] . 'Id');
                        else:
                            $data = Inflector::underscore(get_class($this)) . '_' . $this->primaryKey();
                        endif;
                        break;
                    default:
                        $data = null;
                endswitch;
                $association[$key] = $data;
            endif;
        endforeach;
        return $association;
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
        return $this->_behaviors[$behavior] = new $behavior($this, $options);
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

    public function insertId() {
        return $this->connection()->insertId();
    }
    
    public function affectedRows() {
        return $this->connection()->affectedRows();
    }
    
    public function escape($value) {
        return $this->connection()->escape($value);
    }
    
    protected function scope($scope, $params, $defaults = array()) {
        if(is_array($scope)) {
            $params = $scope;
            $scope = 'default';
        }
        
        if($scope !== false) {
            $scope_name = $scope . 'Scope';
            $scope = $this->{$scope_name};
        }
        else {
            $scope = array();
        }
        
        return array_merge($defaults, $scope, $params);
    }
    
    public function all($scope = null, $params = array()) {
        $defaults = array( 'table' => $this->table() );
        $params = $this->scope($scope, $params, $defaults);
        
        $query = $this->connection()->read($params);

        $results = array();
        while($result = $query->fetch()) {
            if($params['orm']) {
                $self = get_class($this);
                $results []= new $self($result);
            }
            else {
                $results []= $result;
            }
        }

        if(!$params['orm'] && $params['recursion'] >= 0) {
            $results = $this->dependent($results, $params['recursion']);
        }
        
        return $results;
    }
    
    public function first($scope = null, $params = array()) {
        $params['limit'] = 1;
        $results = $this->all($scope, $params);

        return empty($results) ? null : $results[0];
    }
    
    public function dependent($results, $recursion = 0) {
        foreach(array_keys($this->associations) as $type):
            if($recursion < 0 and ($type != 'belongsTo' && $recursion <= 0)) continue;
            foreach($this->{$type} as $name => $association):
                foreach($results as $key => $result):
                    $name = Inflector::underscore($name);
                    $model = $association['className'];
                    $params = array();
                    if($type == 'belongsTo'):
                        $params['conditions'] = array(
                            $association['primaryKey'] => $result[$association['foreignKey']]
                        );
                        $params['recursion'] = $recursion - 1;
                    else:
                        $params['conditions'] = array(
                            $association['foreignKey'] => $result[$association["primaryKey"]]
                        );
                        $params['recursion'] = $recursion - 2;
                        if($type == 'hasMany'):
                            $params['limit'] = $association['limit'];
                            $params['order'] = $association['order'];
                        endif;
                    endif;
                    $result = $this->_models[$model]->all($params);
                    if($type != 'hasMany' && !empty($result)):
                        $result = $result[0];
                    endif;
                    $results[$key][$name] = $result;
                endforeach;
            endforeach;
        endforeach;
        return $results;
    }
    
    public function count($scope = null, $params = array()) {
        $defaults = array( 'table' => $this->table() );
        $params = $this->scope($scope, $params, $defaults);
        unset($params['offset'], $params['limit']);
        
        return $this->connection()->count($params);
    }
    
    public function paginate($scope = null, $params = array()) {
        $count = $this->count($scope, $params);

        $defaults = array(
            'perPage' => $this->perPage,
            'page' => 1
        );
        $params = $this->scope($scope, $params, $defaults);

        $params['offset'] = ($params['page'] - 1) * $params['perPage'];
        $params['limit'] = $params['perPage'];

        $this->pagination = array(
            'totalRecords' => $count,
            'totalPages' => ceil($count / $params['perPage']),
            'perPage' => $params['perPage'],
            'offset' => $params['offset'],
            'page' => $params['page']
        );

        return $this->all(false, $params);
    }
    
    public function toList($scope = null, $params = array()) {
        $defaults = array(
            'key' => $this->primaryKey(),
            'displayField' => $this->displayField,
            'table' => $this->table()
        );
        $params = $this->scope($scope, $params, $defaults);
        
        if(!array_key_exists('fields', $params)) {
            $params['fields'] = array_merge(
                (array) $params['key'],
                (array) $params['displayField']
            );
        }

        $all = $this->connection()->read($params);

        $results = array();
        while($result = $all->fetch()) {
            if(is_array($params['displayField'])) {
                $keys = array_flip($params['displayField']);
                $value = array_intersect_key($result, $keys);
            }
            else {
                $value = $result[$params['displayField']];
            }
            
            $results[$result[$params['key']]] = $value;
        }

        return $results;
    }
    
    public function exists($conditions) {
        return (bool) $this->count(array(
            'conditions' => $conditions
        ));
    }
    
    public function insert($data) {
        $params = array(
            'values' => $data,
            'table' => $this->table()
        );

        return $this->connection()->create($params);
    }
    
    public function update($params, $data) {
        $params += array(
            'values' => $data,
            'table' => $this->table()
        );

        return $this->connection()->update($params);
    }

    public function save($data = array()) {
        if(!empty($data)) {
            $this->data = $data;
        }
        
        if(!is_null($this->id)):
            $this->data[$this->primaryKey()] = $this->id;
        endif;

        // apply modified timestamp
        $date = date('Y-m-d H:i:s');
        if(!array_key_exists('modified', $this->data)):
            $this->data['modified'] = $date;
        endif;

        // verify if the record exists
        $exists = $this->exists(array(
            $this->primaryKey() => $this->id
        ));

        // apply created timestamp
        if(!$exists && !array_key_exists('created', $this->data)):
            $this->data['created'] = $date;
        endif;

        // apply beforeSave filter
        $this->data = $this->fireFilter('beforeSave', $this->data);
        if(!$this->data):
            return false;
        endif;

        // filter fields that are not in the schema
        $data = array_intersect_key($this->data, $this->schema());

        // update a record if it already exists...
        if($exists):
            $save = $this->update(array(
                'conditions' => array(
                    $this->primaryKey() => $this->id
                ),
                'limit' => 1
            ), $data);
        // or insert a new one if it doesn't
        else:
            $save = $this->insert($data);
            $this->id = $this->insertId();
        endif;

        // fire afterSave action
        $this->fireAction('afterSave');

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
    
    public function delete($id, $dependent = true) {
        $params = array(
            'conditions' => array(
                $this->primaryKey() => $id
            ),
            'limit' => 1
        );
        if($this->exists(array($this->primaryKey() => $id)) && $this->deleteAll($params)):
            if($dependent):
                $this->deleteDependent($id);
            endif;
            return true;
        endif;
        return false;
    }
    
    public function deleteDependent($id) {
        foreach(array('hasOne', 'hasMany') as $type):
            foreach($this->{$type} as $model => $assoc):
                $this->{$assoc['className']}->deleteAll(array(
                    'conditions' => array(
                        $assoc['foreignKey'] => $id
                    )
                ));
            endforeach;
        endforeach;
        return true;
    }
    
    public function deleteAll($params = array()) {
        $db = $this->connection();
        $params += array(
            'table' => $this->table()
        );
        return $db->delete($params);
    }
}