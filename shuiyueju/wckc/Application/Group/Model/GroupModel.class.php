<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 14-3-8
 * Time: PM4:14
 */

namespace Group\Model;

use Think\Model;

class GroupModel extends Model
{
    protected $_validate = array(
        array('title', '1,99999', '标题不能为空', self::EXISTS_VALIDATE, 'length'),
        array('title', '0,100', '标题太长', self::EXISTS_VALIDATE, 'length'),
    );

    protected $_auto = array(
        array('post_count', '0', self::MODEL_INSERT),
        array('create_time', NOW_TIME, self::MODEL_INSERT),
    );


    public function editGroup($data) {

        $data = $this->create($data);

        if(!$data) return false;

        $data['title']=op_t($data['title']);

        return $this->save($data);
    }

    public function createGroup($data) {

        $data = $this->create($data);

        //对帖子内容进行安全过滤
        if(!$data) return false;
        $data['title']=op_t($data['title']);
        $result = $this->add($data);
        action_log('add_group','Group',$result,is_login());
        if(!$result) {
            return false;
        }
        S('group_list',null);
        S('my_group_list', null);
        //返回帖子编号
        return $result;
    }

}
