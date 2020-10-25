<?php 
namespace App\Imports;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;


class UsersImport implements OnEachRow
{
    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $row      = $row->toArray();
        \Log::info($row);
        print_r($row);
        return $row;
    }

}