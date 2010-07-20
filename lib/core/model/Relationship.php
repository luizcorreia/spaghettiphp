<?php

require 'lib/core/model/relationships/BelongsTo.php';
require 'lib/core/model/relationships/HasOne.php';
require 'lib/core/model/relationships/HasMany.php';
require 'lib/core/model/relationships/HasAndBelongsToMany.php';

class Relationship {
    protected $properties = array();
    protected $model;
    
    public function __construct($properties) {
        $this->properties = $this->normalize($properties);
    }
    public function normalize($properties) {
        if(is_string($properties)):
            $properties = array('className' => $properties);
        elseif(!array_key_exists('className', $properties)):
            $properties['className'] = $properties['name'];
        endif;
        
        return $properties;
    }
    public function load() {
        $model = $this->properties['className'];
        // @todo check for errors here!
        if(!array_key_exists($model, Model::$instances)):
            Model::$instances[$model] = Loader::instance('Model', $model);
        endif;
        
        return $this->model = Model::$instances[$model];
    }
    public function find() {}
    public function create() {}
    public function update() {}
    public function delete() {}
}