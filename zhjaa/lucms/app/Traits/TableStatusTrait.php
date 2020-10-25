<?php

namespace App\Traits;

use Config;

trait TableStatusTrait
{

    public function formatTablesAllStatus($table_name, $data = [])
    {

        $source_data = Config::get('table_status');
        if (empty($table_name) || !isset($source_data[$table_name])) return $data;
        foreach ($source_data[$table_name] as $key => $val) {
            if (isset($data[$key])) {
                $data[$key . '_text'] = $val[$data[$key]];
            }
        }
        return $data;
    }

    public function getBaseStatus($table_name, $column = '')
    {
        $source_data = Config::get('table_status');
        if ($column) {
            return $source_data[$table_name][$column];
        }
        return $source_data[$table_name];
    }
}
