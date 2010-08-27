<?php

require 'lib/core/model/relationships/BelongsTo.php';
require 'lib/core/model/relationships/HasOne.php';
require 'lib/core/model/relationships/HasMany.php';
require 'lib/core/model/relationships/HasAndBelongsToMany.php';

class Relationship {
    protected $options = array();
    protected $model;
    
    public function __construct($options) {
        $this->options = $this->normalize($options);
        $this->load();
        $this->setOptions();
    }
    public function normalize($options) {
        if(is_string($options)):
            $options = array('model' => $options);
        elseif(!array_key_exists('model', $options)):
            $options['model'] = $options['name'];
        endif;
        
        return $options;
    }
    public function load() {
        return $this->model = Model::load($this->options['model']);
    }
    public function setOptions() {}
    public function find() {}
    public function create() {}
    public function update() {}
    public function delete($foreign_key, $options = array()) {}
    protected function foreignKey($model) {
        return Inflector::underscore($model) . '_id';
    }
}