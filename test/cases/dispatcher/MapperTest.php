<?php

require_once 'PHPUnit/Framework.php';
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config/test.php';

class MapperTest extends PHPUnit_Framework_TestCase {
    public static $defaults = array();
    
    public static function setUpBeforeClass() {
        self::$defaults = array(
            'controller' => Mapper::root(),
            'action' => 'index'
        );
    }

    /**
     * @testdox parse should return controller and action for root URL
     */
    public function testParseShouldReturnControllerAndActionForRootUrl() {
        $expected = self::$defaults;
        $actual = Mapper::parse('/');
        
        $this->assertEquals($expected, $actual);
    }
}
