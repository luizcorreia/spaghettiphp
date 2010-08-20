<?php

require_once 'PHPUnit/Framework.php';
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config/bootstrap.php';
require_once 'test/classes/DatabaseTestCase.php';

class RelationshipTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->relationship = new Relationship('Users');
    }
    
    /**
     * @testdox normalize should transform string into array with className
     */
    public function testNormalizeShouldTransformStringIntoArrayWithClassName() {
        $expected = array('model' => 'Model');
        $actual = $this->relationship->normalize('Model');
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox normalize should add className if only name is provided
     */
    public function testNormalizeShouldAddClassNameIfOnlyNameIsProvided() {
        $expected = array('name' => 'Model', 'model' => 'Model');
        $actual = $this->relationship->normalize(array('name' => 'Model'));
        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox load should load the correct model
     */
    public function testLoadShouldLoadTheCorrectModel() {
        $actual = $this->relationship->load();
        $this->assertTrue($actual instanceof Users);
    }
}