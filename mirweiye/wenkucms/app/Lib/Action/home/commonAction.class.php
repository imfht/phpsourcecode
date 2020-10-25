<?php
class commonAction extends frontendAction {
    public function _initialize() {
        parent::_initialize();
        global $userinfo;
        $userinfo = $this->visitor->info;
    }
    public function getappkey() {
        $data['appsecret'] = C('wkcms_appsecret');
        $data['appkey'] = C('wkcms_appkey');
        echo json_encode($data);
    }
    //验证码结束，收藏等各类点击功能效果操作开始
    public function operate() {
        global $userinfo;
        $data['itemid'] = $this->_request('id', 'intval');
        $data['typeid'] = $this->_request('typeid', 'intval');
        $data['type'] = $this->_request('type', 'intval');
        $num = D('itemlog')->where($data)->count() + 1;
        $data['uid'] = $userinfo['uid'];
        if ($data['uid'] <= 0 || $data['uid'] == '') {
            $this->ajaxReturn(0, '您还未登陆或者注册');
        }
        switch ($data['type']) {
            case 2:
                $msg = '收藏成功';
                $msgerror = '收藏失败';
            break;
            case 1:
                $msg = '下载成功';
                $msgerror = '下载失败';
            break;
            case 3:
                $msg = '推荐成功';
                $msgerror = '推荐失败';
            break;
            default:
            break;
        }
        if (false === D('itemlog')->add($data)) {
            $this->ajaxReturn(0, $msgerror);
        } else {
            $this->ajaxReturn(1, $msg, $num);
        }
    }
    public function deloperate() {
        global $userinfo;
        $data['itemid'] = $this->_request('id', 'intval');
        $data['typeid'] = $this->_request('typeid', 'intval');
        $data['type'] = $this->_request('type', 'intval');
        $data['uid'] = $userinfo['uid'];
        if (false === D('itemlog')->where($data)->delete($data)) {
            $this->ajaxReturn(0, '删除失败');
        } else {
            $this->ajaxReturn(1, '删除成功');
        }
    }
    public function youyong() {
        $map['id'] = $this->_request('id', 'intval');
        if (cookie('youyong') == 1) {
            $data['status'] = 0;
            $data['msg'] = '您已经进行过该操作了！';
            //$this->ajaxReturn(0,'您已经进行过该操作了！');
            
        } else {
            if (false == D('comment')->where($map)->setInc('youyong', 1)) {
                $data['status'] = 0;
                $data['msg'] = '操作失败';
                //$this->ajaxReturn(0,'操作失败');
                
            } else {
                cookie('youyong', '1');
                $data['status'] = 1;
                $data['msg'] = '操作成功';
                //$this->ajaxReturn(1,'操作成功');
                
            }
        }
        echo json_encode($data);
    }
    //验证码开始
    public function code() {
        $image = new Image();
        $height = 30;
        $image->buildImageVerify($length = 4, $mode = 1, $type = 'png', $width = 50, $height = 25, $verifyName = 'captcha');
    }
    /**
     * ajax获取标签
     */
    public function ajax_gettags() {
        $title = $this->_get('title', 'trim');
        if ($title) {
            $tags = D('tag')->get_tags_by_title($title);
            $tags = implode(' ', $tags);
            $this->ajaxReturn(1, L('operation_success'), $tags);
        } else {
            $this->ajaxReturn(0, L('operation_failure'));
        }
    }
    //评论开始
    public function comment($id, $typeid, $sort = '', $order = '') {
        $mod = D('comment');
        $map['itemid'] = $id;
        $map['typeid'] = $typeid;
        if ($sort && $order) {
            $data = $this->_list(0, $mod, $map, $sort, $order, '');//最后一个参数是每页条数，留空是全部  - wfgo备注
        } else {
            $data = $this->_list(0, $mod, $map, '', '', '', '');//最后一个参数是每页条数，留空是全部  - wfgo备注
        }
        
        $commentlist = $data['list'];
        foreach ($commentlist as $key => $value) {
            if ($value['toid'] > 0) {
                $map['id'] = $value['toid'];
                $sub = $mod->where($map)->find();
                $commentlist[$key]['subinfo'] = $sub['info'];
                $commentlist[$key]['subid'] = $sub['commentid'];
            }
        }
        $data['list'] = $commentlist;
        return $data;
    }
    public function add_comment() {
        print_r($_POST);
        $mod = D('comment');
        //echo json_encode($data);
        //$this->ajaxReturn(0, $mod->getError());
        if (false === $data = $mod->create()) {
            $result['status'] = 0;
            $result['msg'] = $mod->getError();
        } else {
            $data['info'] = $this->_request('info');
            $data['info'] = kindcode($data['info']);
            $data['commentid'] = $data['commentid'] + 1;
            if ($data['uid'] == null) {
                $result['status'] = 1;
                $result['msg'] = '您还未登陆';
            } elseif ($data['info'] == null) {
                $result['status'] = 0;
                $result['msg'] = '评论为空';
            } else {
                if ($mod->add($data)) {
                    $result['status'] = 1;
                    $result['msg'] = '感谢您的评论';
                } else {
                    $result['status'] = 1;
                    $result['msg'] = '评论失败';
                }
            }
        }
        echo json_encode($result);
    }
    public function ajaxadd_comment() {
        $mod = D('comment');
        if (false === $data = $mod->create()) {
            IS_AJAX && $this->ajaxReturn(0, $mod->getError());
        } else {
            $data['info'] = $this->_request('info');
            $data['info'] = kindcode($data['info']);
            $data['commentid'] = $data['commentid'] + 1;
            global $userinfo;
            $data['uid'] = $userinfo['uid'];
            if ($data['uid'] == null) {
                IS_AJAX && $this->ajaxReturn(0, '您还未登陆');
            } elseif ($data['info'] == null) {
                IS_AJAX && $this->ajaxReturn(0, '评论内容为空');
            } else {
                if ($mod->add($data)) {
                    IS_AJAX && $this->ajaxReturn(1, '感谢您的评论', '', 'mycomment');
                } else {
                    IS_AJAX && $this->ajaxReturn(0, '添加评论失败');
                }
            }
        }
    }
    public function ajaxadd_jubao() {
        $mod = D('jubao');
        if (false === $data = $mod->create()) {
            IS_AJAX && $this->ajaxReturn(0, $mod->getError());
        } else {
            global $userinfo;
            $data['uid'] = $userinfo['uid'];
            if ($data['uid'] == null) {
                IS_AJAX && $this->ajaxReturn(0, '您还未登陆');
            } else {
                $where = $data;
                unset($where['content']);
                unset($where['add_time']);
                $num = $mod->where($where)->find();
                if ($num) {
                    IS_AJAX && $this->ajaxReturn(0, '您已经举报过了');
                } else {
                    if ($mod->add($data)) {
                        IS_AJAX && $this->ajaxReturn(1, '举报成功');
                    } else {
                        IS_AJAX && $this->ajaxReturn(0, '举报失败');
                    }
                }
            }
        }
    }
    public function ajaxdel_jubao() {
        $mod = D('jubao');
        if (false === $data = $mod->create()) {
            IS_AJAX && $this->ajaxReturn(0, $mod->getError());
        } else {
            global $userinfo;
            $data['uid'] = $userinfo['uid'];
            if ($data['uid'] == null) {
                IS_AJAX && $this->ajaxReturn(0, '您还未登陆');
            } else {
                unset($data['content']);
                unset($data['add_time']);
                $num = $mod->where($data)->find();
                if (!$num) {
                    IS_AJAX && $this->ajaxReturn(0, '您还未举报过');
                } else {
                    if ($mod->where($data)->delete()) {
                        IS_AJAX && $this->ajaxReturn(1, '取消举报成功');
                    } else {
                        IS_AJAX && $this->ajaxReturn(0, '取消举报失败');
                    }
                }
            }
        }
    }
    //评论结束,读取表情配置信息
    public function emot($theme) {
        $public_path = C('PUBLIC_PATH');
        $emot_dir = $public_path . 'images/emot/';
        $info = include_once ($emot_dir . $theme . '/info.php');
        return $info;
    }
    /**
     * ajax检测会员是否存在
     */
    public function ajax_check_name() {
        $name = $this->_get('username', 'trim');
        $id = $this->_get('id', 'intval');
        if (D('user')->name_exists($name, $id)) {
            $this->ajaxReturn(0, '该会员已经存在');
        } else {
            $this->ajaxReturn();
        }
    }
    /**
     * ajax检测邮箱是否存在
     */
    public function ajax_check_email() {
        $name = $this->_get('email', 'trim');
        $id = $this->_get('id', 'intval');
        if (D('user')->email_exists($name, $id)) {
            $this->ajaxReturn(0, '该邮箱已经存在');
        } else {
            $this->ajaxReturn();
        }
    }
    public function ajax_check_title() {
        $title = $this->_get('title', 'trim');
        $id = $this->_get('id', 'intval');
        if (D('doc_con')->title_exists($title, $id)) {
            $this->ajaxReturn(0, '该标题已经存在');
        } else {
            $this->ajaxReturn();
        }
    }


    /**
     * ajax检测会员是否存在,应用到jq的validform插件
     */
    public function ajax_validform_name() {
        $name = $this->_POST('param', 'trim');
        $id = $this->_POST('id', 'intval');
        if (D('user')->name_exists($name, $id)) {
            echo '{"info":"用户名已存在","status":"n"}';
        } else {
            echo '{"info":"","status":"y"}';
        }
    }

    /**
     * ajax检测邮箱是否存在,应用到jq的validform插件
     */
    public function ajax_validform_email() {
        $name = $this->_POST('param', 'trim');
        $id = $this->_POST('id', 'intval');
        if (D('user')->email_exists($name, $id)) {
            echo '{"info":"该邮箱已经存在","status":"n"}';
        } else {
            echo '{"info":"","status":"y"}';
        }
    }
    protected function _upload_init($upload) {
        $file_type = empty($_GET['dir']) ? 'image' : trim($_GET['dir']);
        $ext_arr = array(
            'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
            'flash' => array('swf', 'flv'),
            'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
            'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'),
        );
        $allow_exts_conf = explode(',', C('fours_attr_allow_exts')); // 读取配置
        $allow_max = C('fours_attr_allow_size'); // 读取配置
        //和总配置取交集
        $allow_exts = array_intersect($ext_arr[$file_type], $allow_exts_conf);
        $allow_max && $upload->maxSize = $allow_max * 1024;   //文件大小限制
        $allow_exts && $upload->allowExts = $allow_exts;  //文件类型限制
        $upload->savePath =  './data/upload/editer/' . $file_type . '/';
        $upload->saveRule = 'uniqid';
        $upload->autoSub = true;
        $upload->subType = 'date';
        $upload->dateFormat = 'Y/m/d/';
        return $upload;
    }
    /**
     * 编辑器上传
     */
    public function editer_upload() {
        $file_type = empty($_GET['dir']) ? 'image' : trim($_GET['dir']);
        $result = $this->_upload($_FILES['file']);

        if ($result['error']) {
            echo json_encode(array('error'=>1, 'message'=>$result['info']));
        } else {
            $url = C('wkcms_site_url').'/data/upload/editer/' . $file_type . '/' . $result['info'][0]['savename'];
            echo '{"code":0,"msg":"成功上传","data":{"src":"'.$url.'"}}';
             
        }
        exit;
    }
}
