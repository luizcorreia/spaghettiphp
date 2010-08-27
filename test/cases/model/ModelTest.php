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
     * @expectedException BadMethodCallException
     */
    public function testCallShouldThrowExceptionForMissingMethods() {
        $this->Users->thisMethodDoesNotExist();
    }
    
    /**
     * @PENDING expectedException MissingModelFieldException
     */
    public function testShouldThrowExceptionWhenFieldNotFoundInSchemaOnGetting() {
        //$undefined = $this->Users->undefined;
    }
    
    /**
     * @PENDING expectedException MissingModelFieldException
     */
    public function testShouldThrowExceptionWhenFieldNotFoundInSchemaOnSetting() {
        //$this->Users->undefined = 'Nothing';
    }
    
    public function testShouldReturnNullIfObjectIsEmpty() {
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

    public function testCreateMethodReturnSelfClassObject() {
        $expected = get_class($this->Users);
        $actual = get_class($this->Users->create());
        $this->assertEquals($expected, $actual);
    }
    
    public function testSaveMethodSetTheFieldsOnInserting() {
        $user = $this->Users->create();
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
        $user = $this->Users->create();
        $user->username = 'username2';
        $user->password = 'password2';
        $user->save();
        
        $user->username = 'username2Update';
        $user->save();
        
        $this->assertEquals('username2Update', $user->username);
        $this->assertEquals('password2', $user->password);
    }
}