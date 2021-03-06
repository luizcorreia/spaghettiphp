<?php

class SpaghettiException extends Exception {
    protected $status = 500;
    protected $details;
    
    public function __construct($message, $code, $details = null) {
        parent::__construct($message, $code);
        $this->details = $details;
    }
    public function header($status) {
        $codes = array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Time-out',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Large',
            415 => 'Unsupported Media Type',
            416 => 'Requested range not satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Time-out'
        );
        header('HTTP/1.1 ' . $status . ' ' . $codes[$status]);
    }
    public function getDetails() {
        return $this->details;
    }
    public function toString() {
        if(ob_get_level()) {
            ob_end_clean();
        }
        $this->header($this->status);
        if(Filesystem::exists('app/views/layouts/error.htm.php')):
            $view = new View();
            return  $view->renderView('app/views/layouts/error.htm.php', array(
                'exception' => $this
            ));
        else:
            echo '<pre>';
            throw new Exception('error layout was not found');
        endif;
    }
}

class MissingException extends SpaghettiException {
    protected $status = 404;
}