<?php defined('BASEPATH') OR exit('No direct script access allowed');

defined("CIPLUS_VERSION") OR define("CIPLUS_VERSION", "2.0");
defined("CIPLUS_DB_PREFIX") OR define("CIPLUS_DB_PREFIX", 'ciplus_');
defined("CIPLUS_PATH") OR define("CIPLUS_PATH", FCPATH . 'plus' . DIRECTORY_SEPARATOR);

class MY_Loader extends CI_Loader {
    public function __construct() {
        parent::__construct();
        $this->add_package_path(CIPLUS_PATH);
        $this->helper(array('ciplus', 'language'));
    }
}