<?php

namespace app\admin\controller;

use app\common\controller\ControllerBase;


class Callback extends ControllerBase
{
    public function groupadd_call_back($result, $data)
    {

        $insert['group_id']    = $result;
        $insert['uid']         = $data['uid'];
        $insert['grade']       = 2;
        $insert['create_time'] = time();
        DB('user_group')->insert($insert);

    }


}
