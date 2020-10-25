<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 1/22/14
 * Time: 11:05 PM
 */

namespace Addons\LocalComment\Model;
use Think\Model;

class LocalCommentModel extends Model {

    /* 用户模型自动验证 */
    protected $_validate = array(
        array('content', '0,10000', '评论内容太长', self::EXISTS_VALIDATE, 'length'),
        array('content', '1,99999', '评论内容不能为空', self::EXISTS_VALIDATE, 'length'),
    );

    /* 用户模型自动完成 */
    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('status', 1, self::MODEL_BOTH),
    );

}