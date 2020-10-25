<?php

namespace Output;

class Json implements OutputBase
{
    /**
     * @param int $status
     * @param string $desc
     * @param array $data
     */
    public static function output($status = 200, $desc = 'success', $data = array())
    {
        header('Content-Type:application/json; charset=utf-8');

        $data = array(
            'status' => (int)$status,
            'desc' => $desc,
            'data' => (array)$data
        );

        exit(json_encode($data, true));
    }
}