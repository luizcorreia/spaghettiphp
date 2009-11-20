<?php
/**
 *  Short description.
 *
 *  @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 *  @copyright Copyright 2008-2010, Spaghetti* Framework (http://spaghettiphp.org/)
 */

class Log extends Object {
    /**
      *  Short description.
      *
      *  @param string $message
      *  @param string $type
      *  @return void
      */
    public static function write($message, $type = 'error') {
        $data = sprintf('[%s] [%s] %s' . PHP_EOL, date('Y-m-d H:i:s'), $type, $message);

        // replace with File class or something like that
        $file = fopen(SPAGHETTI_ROOT . '/tmp/log/error', 'a');
        fwrite($file, $data);
        fclose($file);
    }
}