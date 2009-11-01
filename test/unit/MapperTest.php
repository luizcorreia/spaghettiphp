<?php

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'bootstrap.php';
import("core.Mapper");

class MapperTest extends PHPUnit_Framework_TestCase {
    public function testMatch() {
        $result = Mapper::match('/:controller/:action', '/controller/action');
        $this->assertTrue($result);
    }
}