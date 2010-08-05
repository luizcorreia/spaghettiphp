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
    
    public function testCreateMethodReturnSelfClassObject() {
        $expected = get_class($this->users);
        $actual = get_class($this->users->create());
        $this->assertEquals($expected, $actual);
    }
    
    public function testSaveMethodSetTheFieldsOnInserting() {
        $user = $this->users->create();
        $user->username = 'username';
        $user->password = 'password';
        $user->save();
        
        $this->assertNotNull($user->id);
        $this->assertNotNull($user->username);
        $this->assertNotNull($user->password);
        $this->assertNotNull($user->created);
        $this->assertNotNull($user->modified);
    }
    
    public function testSaveMethodSetTheFieldsOnUpdate() {
        $user = $this->users->create();
        $user->username = 'username2';
        $user->password = 'password2';
        $user->save();
        
        $user->username = 'username2Update';
        $user->save();
        
        $this->assertEquals('username2Update', $user->username);
        $this->assertEquals('password2', $user->password);
    }
}
