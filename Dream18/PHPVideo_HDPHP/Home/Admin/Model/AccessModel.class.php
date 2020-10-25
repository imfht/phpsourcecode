<?php

/**
 * 权限处理模型
 * Class AccessModel
 * 楚羽幽  <Name_Cyu@Foxmail.com>
 */
class AccessModel extends Model
{
    public $table = 'access';

    public function editAccess()
    {
        $rid = Q('rid', 0, 'intval');
        if ($rid) {
            /**
             * 删除权限
             */
            M('access')->where(array("rid" => $this->rid))->del();
            foreach ($_POST['nid'] as $v) {
                $data = array("rid" => $rid, "nid" => $v);
                $this->add($data);
            }
            return true;
        } else {
            $this->error = '参数错误';
        }
    }
}