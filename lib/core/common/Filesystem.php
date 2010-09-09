<?php

class Filesystem {
    protected static $rewrite = array(
        'GB' => 1073741824,
        'MB' => 1048576,
        'KB' => 1024,
        'bytes' => 1
    );

    public static function read($file) {
        $file = self::path($file);
        if(self::exists($file)):
            return file_get_contents($file);
        else:
            return null;
        endif;
    }
    public static function write($file, $content = '', $append = false) {
        $file = self::path($file);
        switch($append):
            case 'append':
                return file_put_contents($file, $content, FILE_APPEND);
            case 'prepend':
                return file_put_contents($file, $content . self::read($file));
            default:
                return file_put_contents($file, $content);
        endswitch;
    }
    public static function copy($source, $destination) {
        if(self::exists($source)):
            if(self::isDir($destination)):
                $destination = $destination . '/' . basename($source);
            endif;
            
            return copy(self::path($source), self::path($destination));
        endif;
        
        return false;
    }
    public static function delete($file, $force = true) {
        $file = self::path($file);
        
        if(self::isDir($file)):
            return self::deleteDir($file, $force);
        elseif(self::exists($file)):
            return unlink($file);
        else:
            return false;
        endif;
    }
    public static function rename($source, $destination) {
        $destination = dirname($source) . '/' . $destination;

        return self::move($source, $destination);
    }
    public static function move($source, $destination) {
        $source = self::path($source);
        $destination = self::path($destination);
        
        if(self::exists($source)):
            return rename($source, $destination);
        endif;

        return false;
    }
    public static function getFiles($path) {
        $path = self::path($path);
        return array_slice(scandir($path), 2);
    }
    public static function size($file, $rewrite = true) {
        if(!self::exists($file)):
            return false;
        endif;

        $size = filesize(self::path($file));

        if($rewrite):
            return self::rewriteSize($size);
        else:
            return $size;
        endif;
    }
    public static function rewriteSize($size) {
        foreach(self::$rewrite as $key => $value):
            if($size >= $value):
                return number_format($size / $value, 2) . ' ' . $key;
            endif;
        endforeach;
    }
    public static function isDir($path) {
        return is_dir(self::path($path));
    }
    public static function deleteDir($dir, $force = true) {
        $dir = self::path($dir);
        $files = self::getFiles($dir);
    
        if(count($files)):
            if($force):
                foreach($files as $file):
                    self::delete($dir . '/' . $file, $force);
                endforeach;
            else:
                return false;
            endif;
        endif;

        return rmdir($dir);
    }
    public static function createDir($dir, $mode = 0755) {
        $dir = self::path($dir);

        if(!self::exists($dir)):
            return mkdir($dir, $mode, true);
        endif;
    }
    public static function isUploadedFile($file) {
        return is_uploaded_file(self::path($file));
    }
    public static function moveUploadedFile($name, $destination) {
        $destination = self::path($destination);

        return move_uploaded_file($name, $destination);
    }
    public static function exists($file) {
        return file_exists(self::path($file));
    }
    public static function hasPermission($file, $permission = 'rwx') {
        $file = self::path($file);
        $functions = array(
            'x' => 'is_executable',
            'r' => 'is_readable',
            'w' => 'is_writeable'
        );
        $permissions = str_split($permission);
        
        foreach($permissions as $permission):
            if(!$functions[$permission]($file)):
                return false;
            endif;
        endforeach;

        return true;
    }
    public static function extension($file) {
        $explode = explode('.', $file);
        
        if(($count = count($explode)) > 1):
            return strtolower($explode[$count - 1]);
        endif;
        
        return null;
    }
    public static function path($path, $absolute = true) {
         if(strpos($path, SPAGHETTI_ROOT) === false && !preg_match('(^[a-z]+:)i', $path, $out)):
            if($absolute):
                $path = SPAGHETTI_ROOT . '/' . $path;
            endif;
        endif;

        $pattern = '(([^:])[/\\\]+|\\\)'; // v.4.3    
        return preg_replace($pattern, '$1/', $path);
    }
}