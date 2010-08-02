<?php

/*
Sql for users table:
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `username` varchar(40) NOT NULL,
  `password` char(40) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;
*/

require_once 'PHPUnit/Framework.php';
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config/bootstrap.php';

Connection::add(array(
    'test' => array(
        'driver' => 'MySql',
        'host' => 'localhost',
        'user' => 'root',
        'password' => 'mazinho123',
        'database' => 'spaghettitest',
        'prefix' => ''
    )
));

class Users extends AppModel {    
    public $connection = 'test';
}

class ModelTest extends PHPUnit_Framework_TestCase {
    public $users = null;
    
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