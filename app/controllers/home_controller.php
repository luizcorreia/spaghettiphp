<?php


class HomeController extends AppController {
    public $uses = array();
    public $autoRender = false;
    
    public function index() {
        Model::load('Stories');
        $story = Stories::create();
        $story->title = 'test';
        $story->save();
        
        pr($story);
    }
}