<?php

class FormHelper extends Helper {
    protected $stack = array();
    
    public function create($object, $action = null, $attr = array()) {
        array_push($this->stack, $object);
        
        $attr += array(
            'action' => Mapper::url($action),
            'method' => 'post'
        );
        
        if($attr['method'] == 'file'):
            $attr['method'] = 'post';
            $attr['enctype'] = 'multipart/form-data';
        endif;
        
        return $this->html->openTag('form', $attr);
    }
    public function close() {
        array_pop($this->stack);
        
        return $this->html->closeTag('form');
    }
    public function label($name, $text = null, $attr = array()) {
        $attr += array(
            'for' => $this->id($name)
        );
        
        if(is_null($text)):
            $text = Inflector::humanize($name);
        endif;
        
        return $this->html->tag('label', $text, $attr);
    }
    public function text($name, $attr = array()) {
        return $this->input($name, 'text', $attr);
    }
    public function textarea($name, $attr = array()) {
        $attr = $this->attributes($name, $attr);
        
        return $this->html->tag('textarea', array_unset($attr, 'value'), $attr);
    }
    public function password($name, $attr = array()) {
        return $this->input($name, 'password', $attr);
    }
    public function hidden($name, $attr = array()) {
        return $this->input($name, 'hidden', $attr);
    }
    public function file($name, $attr = array()) {
        return $this->input($name, 'file', $attr);
    }
    public function checkbox($name, $attr = array()) {
        $attr += array(
            'value' => 1
        );

        $hidden = $this->hidden($name, array(
            'id' => false,
            'value' => 0
        ));
        $checkbox = $this->input($name, 'checkbox', $attr);
        
        return $hidden . $checkbox;
    }
    public function radio($name, $value, $attr = array()) {
        $attr += array(
            'value' => $value,
            'id' => $this->id($name) . '_' . Inflector::underscore($value),
            'checked' => $value == $this->value($name)
        );
        
        return $this->input($name, 'radio', $attr);
    }
    public function select($name, $options = array(), $attr = array()) {
        $attr = $this->attributes($name, $attr);
        
        if(array_key_exists('empty', $attr)):
            $options = $this->addEmptyOption($options, array_unset($attr, 'empty'));
        endif;
        
        $options = $this->options($options, array_unset($attr, 'value'));
        
        return $this->html->tag('select', $options, $attr);
    }
    public function submit($value, $attr = array()) {
        $attr += array(
            'name' => 'commit',
            'type' => 'submit',
            'value' => $value
        );
        
        return $this->html->tag('input', null, $attr);
    }
    public function imagesubmit($value, $attr = array()) {
        $attr += array(
            'name' => 'commit',
            'type' => 'image',
            'src' => $this->assets->image($value)
        );
        
        return $this->html->tag('input', null, $attr);
    }
    public function button($value, $type = 'submit', $attr = array()) {
        $attr += array(
            'name' => 'commit',
            'type' => $type
        );
        
        return $this->html->tag('button', $value, $attr);
    }
    protected function input($name, $type, $attr) {
        $defaults = array(
            'type' => $type
        );
        $attr = $this->attributes($name, $attr, $defaults);
        
        
        return $this->html->tag('input', null, $attr);
    }
    protected function model() {
        $object = end($this->stack);
        
        if(is_object($object)):
            return $object;
        endif;
        
        return false;
    }
    public function modelname() {
        $object = end($this->stack);
        
        if(is_object($object)):
            $object = get_class($object);
        endif;
        
        return Inflector::underscore($object);
    }
    protected function id($id) {
        return $this->modelname() . '_' . $id;
    }
    protected function name($name) {
        return $this->modelname() . '[' . $name . ']';
    }
    protected function value($name) {
        if($model = $this->model()):
            if(!is_null($model->{$name})):
                return $model->{$name};
            endif;
        endif;
        
        return false;
    }
    protected function attributes($name, $attr, $defaults = array()) {
        $field_defaults = array(
            'value' => $this->value($name),
            'id' => $this->id($name),
            'name' => $this->name($name)
        );
        
        return array_merge($field_defaults, $defaults, $attr);
    }
    protected function options($options, $selected) {
        $content = '';
        foreach($options as $value => $text):
            $attr = compact('value');
            
            if($value === $selected):
                $attr['selected'] = true;
            endif;
            
            $content .= $this->html->tag('option', $text, $attr);
        endforeach;
        
        return $content;
    }
    protected function addEmptyOption($options, $empty) {
        if(is_array($empty)):
            $options = $empty + $options;
        else:
            if($empty === true):
                $empty = '';
            endif;
        
            array_unshift($options, $empty);
        endif;
        
        return $options;
    }
}