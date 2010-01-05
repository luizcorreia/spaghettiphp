<?php

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'bootstrap.php';

class StringTest extends PHPUnit_Framework_TestCase {
    public function testInsert() {
        $expected = '/home/index';
        $result = String::insert('/:controller/:action', array(
            'controller' => 'home',
            'action' => 'index'
        ));
        
        $this->assertEquals($expected, $result);
    }
    public function testInsertWithDoubleKeys() {
        $expected = '/home/index/home';
        $result = String::insert('/:controller/:action/:controller', array(
            'controller' => 'home',
            'action' => 'index'
        ));
        
        $this->assertEquals($expected, $result);
    }
    public function testExtract() {
        $expected = array('controller', 'action');
        $result = String::extract('/:controller/:action');
        
        $this->assertEquals($expected, $result);
    }
}