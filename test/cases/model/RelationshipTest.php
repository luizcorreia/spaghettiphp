<?php

require_once 'PHPUnit/Framework.php';
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config/bootstrap.php';

class RelModel extends AppModel {
    protected $table = false;
}

class RelationshipTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->relationship = new Relationship('RelModel');
    }
    public function tearDown() {
        $this->relationship = null;
    }
    
    /**
     * @testdox normalize should transform string into array with className
     */
    public function testNormalizeShouldTransformStringIntoArrayWithClassName() {
        $expected = array('className' => 'Model');
        $actual = $this->relationship->normalize('Model');
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox normalize should add className if only name is provided
     */
    public function testNormalizeShouldAddClassNameIfOnlyNameIsProvided() {
        $expected = array('name' => 'Model', 'className' => 'Model');
        $actual = $this->relationship->normalize(array('name' => 'Model'));
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox load should load the correct model
     */
    public function testLoadShouldLoadTheCorrectModel() {
        $actual = $this->relationship->load();
        $this->assertTrue($actual instanceof RelModel);
    }
}