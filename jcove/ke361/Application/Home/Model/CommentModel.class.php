<?php
namespace Home\Model;

use Think\Model;

class CommentModel extends Model
{
    protected $_auto = array(
        array('uid', UID, self::MODEL_INSERT),
        array('create_time', NOW_TIME, self::MODEL_INSERT),
    );
    protected $_validate = array(
        array('content', '2,400', '评论内容为2-400个字符', self::EXISTS_VALIDATE, 'length'),
        );
    public function addComment(){
        if($this->create()){
            return $this->add();
        }else{
            return false;
        }
    }
}

?>