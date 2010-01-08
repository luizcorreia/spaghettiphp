<?php

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'bootstrap.php';
import('core.dispatcher.Mapper');

class MapperTest extends PHPUnit_Framework_TestCase {
    public function testMatchUrlWithRoute() {
        $check = String::insert('/:controller/:action', array(
            'controller' => '([a-z-_]+)',
            'action' => '([a-z-_]+)'
        ));
        $result = Mapper::match($check, '/controller/action');

        $this->assertTrue($result);
    }
    public function testParseRootUrl() {
        $expected = array(
            'controller' => 'home',
            'action' => 'index'
        );
        $result = Mapper::parse('/');
        
        $this->assertEquals($expected, $result);
    }
    public function testParseOnlyController() {
        $expected = array(
            'controller' => 'home',
            'action' => 'index'
        );
        $result = Mapper::parse('/home');
        
        $this->assertEquals($expected, $result);
    }
    public function testParseControllerAndAction() {
        $expected = array(
            'controller' => 'controller',
            'action' => 'action'
        );
        $result = Mapper::parse('/controller/action');
        
        $this->assertEquals($expected, $result);
    }
    public function testParseControllerAndActionAndId() {
        $expected = array(
            'controller' => 'controller',
            'action' => 'action',
            'id' => 1
        );
        $result = Mapper::parse('/controller/action/1');
        
        $this->assertEquals($expected, $result);
    }
    public function testParseControllerAndId() {
        $expected = array(
            'controller' => 'controller',
            'action' => 'index',
            'id' => 1
        );
        $result = Mapper::parse('/controller/1');
        
        $this->assertEquals($expected, $result);
    }
}