<?php


class HomeController extends AppController {
    public $uses = array();
    public $autoRender = false;
    
    public function index() {
        Model::load('Stories');
        Stories::update(array(
            'conditions' => array(
                'id' => 1
            )
        ), array(
            'title' => 'Woops'
        ));
        pr(Stories::find(1));
    }
}