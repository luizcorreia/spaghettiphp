<?php
/**
 *  Short description.
 *
 *  @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 *  @copyright Copyright 2008-2009, Spaghetti* Framework (http://spaghettiphp.org/)
 */

class Loader {
    /**
      *  Short description.
      *
      *  @param string $file
      *  @return boolean
      */
    public static function import($file, $extension = 'php') {
        $file = str_replace('.', DS, $file);
        return require_once $file . '.' . $extension;
    }
}

/**
  *  Short description.
  *
  *  @param string $file
  *  @return boolean
  */
function import($file, $extension = 'php') {
    return Loader::import($file, $extension);
}