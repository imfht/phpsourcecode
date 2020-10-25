<?php
!defined('FRAMEWORK_PATH') && exit('Access Deined.');

class api_control extends base_www {

    /**
     * store data file
     *
     * @var string
     */
    private $cache_file = 'data/todo.json';

    function __construct(&$conf) {
        // check form hash
        $action            = core::R('a');
        $check_hash_action = array('list', 'edit');
        if (in_array($action, $check_hash_action) && !misc::form_submit()) {
            $this->show_json('FormHashLost');
        }
        parent::__construct($conf);
        $this->cache_file = ROOT_PATH . $this->cache_file;
    }

    /**
     * list of all data
     */
    public function on_list() {

        if (!is_file($this->cache_file)) {
            // init form
            $list = array(
                array(
                    'todo'      => '5：00 出门吃烤肉',
                    'completed' => 0,
                    'dateline'  => time() - 3600,
                ),
                array(
                    'todo'      => '写5000行代码',
                    'completed' => time(),
                    'dateline'  => time() - 2000,
                ),
                array(
                    'todo'      => '找个妹子表白',
                    'completed' => 0,
                    'dateline'  => time() - 1000,
                ),
            );
        } else {
            $list = json_decode(file_get_contents($this->cache_file), 1);
        }
        $extend = array(
            'list' => $list,
            // some config for user
            'conf' => array(),
        );
        $this->show_json('', 0, $extend);
    }

    /**
     * save data
     */
    public function on_edit() {
        $data = json_decode(core::R('data'), 1);
        file_put_contents($this->cache_file, json_encode($data));
        $this->show_json('', 0);
    }
}


?>