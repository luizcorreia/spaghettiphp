<?php

class MissingControllerException extends MissingException {
    public function __construct($details = array()) {
        $message = 'Missing Controller';
        $details = 'The controller <code>' .  $details['controller']. '</code> was not found.';
        parent::__construct($message, 0, $details);
    }
}

class MissingActionException extends MissingException {
    public function __construct($details = array()) {
        $message = 'Missing Action';
        $details = 'The action <code>' . $details['controller'] . '::' .  $details['action']. '()</code> was not found.';
        parent::__construct($message, 0, $details);
    }
}