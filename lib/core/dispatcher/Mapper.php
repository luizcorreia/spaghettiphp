<?php

class Mapper {
    protected static $root;
    
    public static function root($root = null) {
        if(is_null($root)):
            return self::$root;
        else:
            self::$root = $root;
        endif;
    }
    public static function parse($url) {
        return array(
            'controller' => self::root(),
            'action' => 'index'
        );
    }
}