<?php

class Users extends AppModel {
    protected $validates = array(
        'username' => array(
            'required' => true,
            'rule' => 'notEmpty'
        )
    );
}
