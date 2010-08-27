<?php

class HasMany extends Relationship {
    public function setOptions() {
        $default = array(
            'foreignKey' => $this->foreignKey($this->options['model']),
            'dependent' => true
        );
        
        $this->options = array_merge($default, $this->options);
    }
    public function delete($foreign_key, $options = array()) {
        $options += $this->options;
        
        if($options['dependent']):
            return $this->model->deleteAll(array(
                'conditions' => array(
                    $options['foreignKey'] => $foreign_key
                )
            ));
        endif;
    }
}