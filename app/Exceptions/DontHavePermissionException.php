<?php
namespace App\Exceptions;

class DontHavePermissionException extends \Exception{
    public function __construct($message = null, $code = 1, \Exception $previous = null){
        // Default message
        $message = $message ?: 'DontHavePermissionException';

        parent::__construct($message, $code, $previous);
    }
}