<?php
/**
 * @package Yaf
 * @author 李枨煊<lcx165@gmail.com> (DOC Only)
 */
class Yaf_Exception extends Exception {

    protected $message;

    protected $code;

    protected $previous;

    /**
     * The getPrevious purpose
     *
     * @return void
     */
    public function getPrevious() {}

    /**
     * The __construct purpose
     */
    public function __construct() {}


}