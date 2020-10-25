<?php

/**
 * Class cli_control
 */
class cli_control extends base_control
{


    function __construct(&$conf) {
        if (!core::is_cmd()) {
            exit('Access Denied');
        }
        parent::__construct($conf);
    }

    /**
     * call undefined method
     *
     * @param $method
     * @param $param
     */
    public function __call($method, $param) {
        $file = $this->get_run_file($method);
        if (!$file || !is_file($file)) {
            log::info('NotFound', $method);
            exit;
        } else {
            include $file;
        }
    }

    /**
     *
     */
    public function clean_memory() {
        unset($_SERVER['sqls'], $_SERVER['cache'], $_SERVER['sphinx'], $_SERVER['log']);
    }

}

?>