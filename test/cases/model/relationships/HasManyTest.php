<?php

require_once 'PHPUnit/Framework.php';
require_once 'SymfonyComponents/YAML/sfYaml.php';
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config/bootstrap.php';
require_once 'test/classes/DatabaseTestCase.php';

class HasManyTest extends DatabaseTestCase {
    public function setUp() {
        parent::setUp();
        
        $this->Users = Model::load('Users');
        $this->Posts = Model::load('Posts');
        $this->relationship = new HasMany(array(
            'name' => 'Posts',
            'foreignKey' => 'user_id'
        ));
    }
    
    /**
     * @testdox delete should delete dependent records if dependent=true
     */
    public function testDeleteShouldDeleteDependentRecordsIfDependentIsTrue() {
        $user = $this->Users->first();
        $this->relationship->delete($user['id']);
        $results = $this->Posts->allByUserId($user['id']);

        $actual = empty($results);
        $this->assertTrue($actual);
    }
    
    /**
     * @testdox delete should not delete dependent records if dependent=false
     */
    public function testDeleteShouldNotDeleteDependentRecordsIfDependentIsFalse() {
        $user = $this->Users->first();
        $this->relationship->delete($user['id'], array('dependent' => false));
        $results = $this->Posts->allByUserId($user['id']);

        $actual = !empty($results);
        $this->assertTrue($actual);
    }
}