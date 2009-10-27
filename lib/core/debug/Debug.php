<?php
/**
 *  Short description.
 *
 *  @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 *  @copyright Copyright 2008-2010, Spaghetti* Framework (http://spaghettiphp.org/)
 */

class Debug {
    public static function errorReporting($level = null) {
        if(is_null($level)):
            $level = Config::read("Debug.level");
        endif;
        switch($level):
            case 3:
                $level = E_ALL | E_STRICT;
                break;
            case 2:
                $level = E_ALL | E_STRICT & ~E_NOTICE;
                break;
            case 1:
                $level = E_ALL & ~E_NOTICE & ~E_DEPRECATED;
                break;
            default:
                $level = 0;
        endswitch;
        ini_set("error_reporting", $level);
    }
    public static function pr() {
        
    }
    public static function dump() {
        
    }
}

function pr() {
    return Debug::pr();
}

function dump() {
    return Debug::dump();
}