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
        return $this->model = Model::load($this->properties['className']);
    }
    public function find() {}
    public function create() {}
    public function update() {}
    public function delete() {}
}