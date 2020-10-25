<?php
/**
 * 网站配置
 */
namespace Admin\Controller;
class WebController extends AdminBaseController {
    /*
     * 邮箱设置
     */
    public function mailbox(){
        $optionModel =  M('Options');
        $where = array('option_name'=>'邮箱配置');
        if (IS_AJAX) {
            //载入配置文件
            $data = include APP_PATH . 'Common/Conf/webconfig.php';
            //邮件配置
            $postData = array(
                'CP_EMAIL_FROM_NAME'  => I('post.sender',''),//发件人
                'CP_EMAIL_SMTP'    => I('post.smtp',''),//SMTP服务器
                'CP_EMAIL_USERNAME' => I('post.loginname',''),//发件箱帐号
                'CP_EMAIL_PASSWORD' => I('post.password',''),//发件邮箱 客户端授权码  需要开启才能使用第三方邮件客户端
            );
            $newData = array_merge($data,$postData);
            //存入文件
            if ($this->DataConfigToFile($newData)) {
                //数据存入
                $optionData = array(
                    'option_name' => '邮箱配置',
                    'option_value' => json_encode($postData)
                );
                if ($optionModel->where($where)->find()) {
                    $optionModel->where($where)->save($optionData);
                } else {
                    $optionModel->add($optionData);
                }
                exit(json_encode(array('status'=>1,'msg'=> '操作成功.^_^')));
            } else {
                exit(json_encode(array('status'=>0,'msg'=> '请检查配置目录是否具有操作权限.^_^')));
            }
        }

        $oldData = $optionModel->where($where)->getField('option_value');
        $this->assign('data',json_decode($oldData,true));
        $this->display();
    }

    /*
     * 邮箱模板
     */
    public function mailbox_tmp(){
        $optionModel =  M('Options');
        $where = array('option_name'=>'邮箱模板');
        $oldData = $optionModel->where($where)->find();
        if (IS_AJAX) {
            $conData = array(
                'title' => I('post.title',''),
                'content' => trim($_POST['content'])
            );

            $optionData = array(
                'option_name' => '邮箱模板',
                'option_value' => json_encode($conData)
            );
            if ($oldData) {
                $optionModel->where($where)->save($optionData);
            } else {
                $optionModel->add($optionData);
            }
            exit(json_encode(array('status'=>1,'msg'=> '操作成功.^_^')));

        }

        $this->assign('data',json_decode($oldData['option_value'],true));
        $this->display();
    }

    /*
     * 网站信息
     */
    public function web_info(){
        if (IS_AJAX) {
            //载入配置文件
            $data = include APP_PATH . 'Common/Conf/webconfig.php';
            $postData = array(
                'MG_TITLENAME'  => I('post.MG_TitleName',''),
                'MG_MAIL'       => I('post.MG_Mail',''),
                'MG_ICP'        => I('post.MG_ICP',''),
                'MG_COPYRIGHT'  => I('post.MG_Copyright','')
            );
            $newData = array_merge($data,$postData);
            //存入文件
            if ($this->DataConfigToFile($newData)) {
                //数据存入
                exit(json_encode(array('status'=>1,'msg'=> '操作成功.^_^')));
            } else {
                exit(json_encode(array('status'=>0,'msg'=> '请检查配置目录是否具有操作权限.^_^')));
            }
        }
        $this->display();
    }

    /*
     * 第三方登录
     */
    public function future(){
        if (IS_AJAX) {
            $type = I('post.type','');
            if (!$type) exit(array('status'=>0,'msg'=>'操作失败.^_^'));
            //载入配置文件
            $data = include APP_PATH . 'Common/Conf/webconfig.php';
            $APPkey = trim(I('post.APPkey'));
            $APPsecret = trim(I('post.APPsecret'));
            $AppCallback = trim(I('post.callback'));
            if ($type == 'QQ') {
                //QQ配置
                $postData = array(
                    'QQ_APP_KEY'    => $APPkey,
                    'QQ_APP_SECRET' => $APPsecret,
                    'QQ_CALLBACK'   => $AppCallback
                );
            }
            if ($type == 'sina') {
                //新浪配置
                $postData = array(
                    'SINA_APP_KEY'    => $APPkey,
                    'SINA_APP_SECRET' => $APPsecret,
                    'SINA_CALLBACK'   => $AppCallback
                );
            }
            $newData = array_merge($data,$postData);
            //存入文件
            if ($this->DataConfigToFile($newData)) {
                //数据存入
                exit(json_encode(array('status'=>1,'msg'=> '操作成功.^_^')));
            } else {
                exit(json_encode(array('status'=>0,'msg'=> '请检查配置目录是否具有操作权限.^_^')));
            }
        }
        $this->display();
    }

}