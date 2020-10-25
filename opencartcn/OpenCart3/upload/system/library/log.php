<?php
/**
 * @package        OpenCart
 * @author        Daniel Kerr
 * @copyright    Copyright (c) 2005 - 2017, OpenCart, Ltd. (https://www.opencart.com/)
 * @license        https://opensource.org/licenses/GPL-3.0
 * @link        https://www.opencart.com
 */

/**
 * Log class
 */
class Log
{
    private $handle;

    /**
     * Constructor
     *
     * @param    string $filename
     */
    public function __construct($filename)
    {
        if (!stripos($filename, '.log')) {
            $filename .= '.log';
        }
        $filePath = DIR_LOGS . $filename;
        if (!file_exists($filePath)) {
            create_dir($filePath);
        }
        $this->handle = fopen(DIR_LOGS . $filename, 'a');
    }

    /**
     *
     *
     * @param    string|array $messages
     */
    public function write(...$messages)
    {
        foreach ($messages as $message) {
            if (PHP_SAPI == 'cli') {
                d($message);
            }
            fwrite($this->handle, date('Y-m-d H:i:s') . ' - ' . print_r($message, true) . "\n");
        }
    }

    public function __destruct()
    {
        fclose($this->handle);
    }

    public function writeException($e)
    {
        if (!is_object($e)) {
            throw new Exception('Invalid exception object!');
        }
        $this->write('PHP Exception: ' . $e->getCode() . ', ' . $e->getMessage());
        $this->write($e->getFile() . "(line:{$e->getLine()})");
    }
}
