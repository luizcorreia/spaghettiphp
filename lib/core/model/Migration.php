<?php

abstract class Migration {
    public static function connection() {
        $connection = Connection::getConfig('default');
        return Connection::get($connection);
    }
    public static function createTable($name, $columns, $options = array()) {
        
    }
    public static function dropTable($name) {
        
    }
    public static function renameTable($old_name, $new_name) {
        
    }
    public static function addColumn($table, $name, $type, $options = array()) {
        
    }
    public static function removeColumn($table, $name) {
        
    }
    public static function renameColumn($table, $old_name, $new_name) {
        
    }
    public static function changeColumn($table, $name, $type, $options = array()) {
        
    }
    public static function addIndex($table, $columns, $options = array()) {
        
    }
    public static function removeIndex($table, $name) {
        
    }
    abstract public static function up();
    abstract public static function down();
}