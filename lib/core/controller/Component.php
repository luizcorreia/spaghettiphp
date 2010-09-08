<?php

class Component {
    public static function load($name, $instance = false) {
        if(!class_exists($name) && Filesystem::exists('lib/components/' . $name . '.php')):
            require_once 'lib/components/' . $name . '.php';
        endif;
        if(class_exists($name)):
            if($instance):
                return new $name();
            else:
                return true;
            endif;
        else:
            $message = 'The component <code>' . $name . '</code> was not found.';
            throw new InternalErrorException('Missing Component', 0, $message);
        endif;
    }
    public function initialize($controller) { }
    public function startup($controller) { }
    public function shutdown($controller) { }
}