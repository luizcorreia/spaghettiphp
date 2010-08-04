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
     * @expectedException MissingModelFieldException
     */
    public function testTheThrowExceptionWhenFieldNotFoundInSchemaOnGetting() {
        $undefined = $this->users->undefined;
    }
    
    /**
     * @expectedException MissingModelFieldException
     */
    public function testTheThrowExceptionWhenFieldNotFoundInSchemaOnSetting() {
        $this->users->undefined = 'Nothing';
    }
    
    public function testTheNullReturnIfObjectIsEmpty() {
        $this->assertNull($this->users->username);
    }
}
