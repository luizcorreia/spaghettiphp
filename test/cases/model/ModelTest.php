<?php

require_once 'PHPUnit/Framework.php';
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config/test.php';
require_once 'test/classes/DatabaseTestCase.php';

class ModelTest extends DatabaseTestCase {
    public function setUp() {
        parent::setUp();
        
        $this->Users = Model::load('Users');
    }

    /**
     * @testdox __call should throw exception for missing methods
     * @expectedException BadMethodCallException
     */
    public function testCallShouldThrowExceptionForMissingMethods() {
        $this->Users->thisMethodDoesNotExist();
    }

    /**
     * @testdox __call should return values to firstBy<field>
     */
    public function testCallShouldReturnValuesToFirstByField() {
        $actual = $this->Users->firstById(1);
        $this->assertType('array', $actual);
    }

    /**
     * @testdox __call should return values to allBy<field>
     */
    public function testCallShouldReturnValuesToAllByField() {
        $actual = $this->Users->allById(1);
        $this->assertType('array', $actual);
    }
    
    /**
     * @testdox __get should return null if value is not set
     */
    public function testGetShouldReturnNullIfValueIsNotSet() {
        $user = $this->Users->create();
        $this->assertNull($user->username);
    }

    /**
     * @testdox toList should return a key/value array
     */
    public function testToListShouldReturnAKeyValueArray() {
        $params = array(
            'key' => 'username',
            'displayField' => 'password'
        );
        $actual = $this->Users->toList($params);
        
        $expected = array(
            'spaghettiphp' => 123456
        );
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox create should be instance of Model
     */
    public function testCreateShouldBeInstanceOfModel() {
        $actual = $this->Users->create();
        $this->assertTrue($actual instanceof Users);
    }
}