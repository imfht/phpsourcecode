<?php
namespace Library\PHPMailer;

use Exception;
/**
 * PHPMailer exception handler
 * @package PHPMailer
 */
class PhpmailerException extends Exception
{
    /**
     * Prettify error message output
     * @return string
     */
    public function errorMessage()
    {
        $errorMsg = '<strong>' . $this->getMessage() . "</strong><br />\n";
        return $errorMsg;
    }
}