<?php

require_once 'PHPUnit/Framework.php';
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config/test.php';

class MapperTest extends PHPUnit_Framework_TestCase {
    /**
     * @testdox dispatch should throw MissingControllerException when controller does not exist
     */
    public function testDispatchShouldThrowMissingControllerException() {
    }
}
