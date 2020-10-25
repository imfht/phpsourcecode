<?php
namespace Modules\User\Models;

use Phalcon\Mvc\Model;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class User extends Model
{

    public static function userList($num, $page)
    {
        return self::find(array(
            'order' => 'changed DESC',
            'limit' => array(
                'number' => $num,
                'offset' => $page * $num
            )
        ));
    }

    public static function findList($num, $page)
    {
        $query = self::find(array(
            'order' => 'created DESC'
        ));
        $pager = new PaginatorModel(array(
            'data' => $query,
            'limit' => $num,
            'page' => $page
        ));
        return $pager->getPaginate();
    }
}