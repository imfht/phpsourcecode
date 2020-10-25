<?php

namespace App\Models\Traits;

trait TableStatusTrait
{
    protected function sourceData()
    {
        return [
            'common_table' => [
                'enable' => [0 => '禁用', 1 => '启用']
            ],
        ];
    }

    public function getTableStatus($model, $column = false)
    {
        $source_data = $this->sourceData();
        if ($column) {
            return $source_data[$model][$column];
        }
        return $source_data[$model];
    }
}
