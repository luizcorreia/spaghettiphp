<?php

require_once 'PHPUnit/Framework.php';
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config/test.php';
require_once 'test/classes/models/Users.php';

class ModelTest extends PHPUnit_Framework_TestCase {
    public static function setUpBeforeClass() {
        $connection = Connection::get('test');
        $connection->query(Filesystem::read('test/sql/users_up.sql'));
    }
    public static function tearDownAfterClass() {
        $connection = Connection::get('test');
        $connection->query(Filesystem::read('test/sql/users_down.sql'));
    }
    public function setUp() {
        $this->users = new Users();
    }
    public function tearDown() {
        $this->users = null;
    }
    
    /**
     * @PENDING expectedException MissingModelFieldException
     */
    public function testShouldThrowExceptionWhenFieldNotFoundInSchemaOnGetting() {
        //$undefined = $this->users->undefined;
    }
    
    /**
     * @PENDING expectedException MissingModelFieldException
     */
    public function testShouldThrowExceptionWhenFieldNotFoundInSchemaOnSetting() {
        //$this->users->undefined = 'Nothing';
    }
    
    public function testShouldReturnNullIfObjectIsEmpty() {
        $this->assertNull($this->users->username);
    }

    /**
     * @testdox toList should return a key/value array
     * @todo refactor this test, maybe use fixtures?
     */
    public function testToListShouldReturnAKeyValueArray() {
        $data = array(
            "username" => "spaghettiphp",
            "password" => 123456
        );
        $this->users->save($data);

        $params = array(
            "key" => "username",
            "displayField" => "password"
        );
        $actual = $this->users->toList($params);
        
        $expected = array(
            "spaghettiphp" => 123456
        );
        $this->assertEquals($expected, $actual);
    }
}
