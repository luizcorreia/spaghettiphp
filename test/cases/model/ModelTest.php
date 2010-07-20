<?php

require_once 'PHPUnit/Framework.php';
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config/bootstrap.php';

class ModelTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        
    }
    public function tearDown() {
        
    }
    
    /**
     * @testdox the truth
     */
    public function testTheTruth() {
        $this->assertTrue(true);
    }
}