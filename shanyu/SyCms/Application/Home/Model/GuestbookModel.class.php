<?php
namespace Home\Model;
use Think\Model;

class GuestbookModel extends Model{

    //自动验证(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
    //验证条件(0:存在字段验证|默认,1:必须验证,2:值不为空验证)
    //验证时间(1:新增验证,2:修改验证,3:全部验证|默认)
    protected $_validate = array(
        //添加
        array('username', 'require', '昵称不能为空'),
        array('mobile','/^\d{11}$/','请正确填写手机号码',1,'regex',3),
        array('content', '0,255', '留言内容请不要超过255个字符',1, 'length',2),

    );

    //内容过滤/填充(完成字段1,完成规则,[完成条件,附加规则,函数参数])
    //完成条件(1:新增时候处理,2:修改时候处理,3:全部时候处理|默认)
    protected $_auto = array (
        array('add_ip','get_client_ip',1,'function'),
        array('add_time','date',1,'function',array('Y-m-d H:i:s')),
    );


}