<?php

require_once 'test/classes/models/Users.php';
require_once 'test/classes/models/Posts.php';

class DatabaseTestCase extends PHPUnit_Framework_TestCase {
    public static function setUpBeforeClass() {
        $connection = Connection::get('test');
        $connection->query(Filesystem::read('test/sql/mysql_up.sql'));
    }
    public static function tearDownAfterClass() {
        $connection = Connection::get('test');
        $connection->query(Filesystem::read('test/sql/mysql_down.sql'));
    }
    public function setUp() {
        $this->loadFixtures();
    }
    public function tearDown() {
        $connection = Connection::get('test');
        $connection->query('TRUNCATE TABLE users');
        $connection->query('TRUNCATE TABLE posts');
    }
    public function loadFixtures() {
        $files = array('posts', 'users');
        foreach($files as $file):
            $model = Model::load(Inflector::camelize($file));
            $records = sfYaml::load(Filesystem::path('test/fixtures/' . $file . '.yaml'));
            foreach($records as $i => $record):
                $model->id = $i;
                $model->save($record);
            endforeach;
        endforeach;
    }
}