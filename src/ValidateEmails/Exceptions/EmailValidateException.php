<?php

namespace OleksiiNikishkin\ValidateEmails\Exceptions;

class EmailValidateException extends \Exception {
    private $_options;

    public function __construct($message, $code = 0, \Exception $previous = null, $options = array()) {
        parent::__construct($message, $code, $previous);

        $this->_options = $options;
    }

    public function GetOptions() {
        return $this->_options;
    }
}