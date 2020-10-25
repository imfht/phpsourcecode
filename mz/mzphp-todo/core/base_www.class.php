<?php

/**
 * Class base_www
 */
class base_www extends base_control {

    /**
     * @param       $data
     * @param int   $error
     * @param array $extend
     */
    function show_message($message, $error = 1, $extend = array()) {
        if (core::R('ajax')) {
            $this->show_json($message, $error, $extend);
        }
        $data = array(
            'error'   => $error,
            'message' => $message,
            'extend'  => $extend,
        );
        VI::assign('data', $data);
        $this->show('show-message');
        exit;
    }

    /**
     * show json
     *
     * @param       $data
     * @param int   $error
     * @param array $extend
     */
    function show_json($data, $error = 1, $extend = array(), $jsonp = 0) {
        if ($data == 1) {
            $error = 0;
        }
        $data = array(
            'error'  => $error,
            'data'   => $data,
            'extend' => $extend,
        );
        if (DEBUG) {
            VI::assign('data', $data);
            VI::assign('jsonp', $jsonp);
            $this->show('show-json');
        } else {
            $response = core::json_encode($data);
            if (core::R('frame')) {
                $response = '<html><body>' . $response . '</body></html>';
            }
            echo $jsonp ? htmlspecialchars($jsonp) . '(' . $response . ');' : $response;
        }
        exit;
    }

}

?>