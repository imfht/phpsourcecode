<?php
/**
 * Created by PhpStorm.
 * User: imust
 * Date: 2016/12/30
 * Time: 22:02
 */

namespace App\Logic;

use Illuminate\Support\Facades\DB;

class PermissionLogic
{
    public function getPermissionsWithPage($countPerPage=15)
    {
        if(is_numeric($countPerPage) && $countPerPage>0)
        {
            return DB::table('permissions')->paginate($countPerPage);
        }
        return null;
    }
}