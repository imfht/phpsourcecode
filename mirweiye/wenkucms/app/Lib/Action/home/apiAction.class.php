<?php
class apiAction extends docbaseAction {
    public function _initialize() {
        parent::_initialize();
        global $userinfo;
        $userinfo = $this->visitor->info;
        $this->assign('uid', $userinfo['uid']);
        $this->_mod = D('doc_con');
        $this->_cate_mod = D('doc_cate');
        $action = $this->_get('action', 'trim');
        $this->assign('action', $action);
    }

    /**
     * 接收文档转换的信息接口
     */
    public function convert() {
        $file_size = $this->_request('file_size');
        $ext = $this->_request('ext');
        $page = $this->_request('page');
        $url = $this->_request('url');
        $img = $this->_request('img');
        $status = $this->_request('status');

        $appid = $this->_request('appid');
        $signature = $this->_request('signature');
        $timestamp = $this->_request('timestamp');
        $nonce = $this->_request('nonce');
        $file = $this->_request('file');
        $file_id = $this->_request('file_id');
        $uid = $this->_request('uid');

        //$appid = C('wkcms_convert_appid');
        import("@.ORG.convert.Core");
        $convert = new ORG\Convert\Core();

        $param = $convert->signature($appid, false, $timestamp, $nonce, $file, $file_id, $uid);

        if ($param['signature'] != $signature) {
            echo 'error';die;
        }

        $map['id'] = $file_id;
        $info = D('doc_con')->where($map)->find(); 

        if ($info) {
            $data['viewurl'] = $url;
            $data['imgurl'] = $img;
            $data['page'] = $page;
            $data['convert_key'] = $file;
            if ($status == 1) {
                $data['convert_status'] = 2;
            } else {
                $data['convert_status'] = 3;
                $data['status'] = 1;
            }
            
            D('doc_con')->where($map)->save($data);
        }

        echo $file_id;die;
    }

    /**
     * 批量转换
     */
    public function mul() {
        import("@.ORG.convert.Core");
        $convert = new ORG\Convert\Core();

        $status = $this->_request('status');
        $num = $this->_request('num');
        if (!$num) {
            $num = '0,1000';
        }
        $map = array();
        $map['convert_status'] = $status ? $status : 1;
        $data = D('doc_con')->where($map)->limit($num)->select();
        $test = $this->_request('test');
        if ($test == 1) {
            print_r($data);die;
        }

        if ($data) {
            foreach ($data as $k => $v) {
                $key = $convert->upload($v);
                if ($key) {
                    $save = array();
                    $save['convert_key'] = $key;
                    D('doc_con')->where(array('id' => $v['id']))->save($save);
                }
            }
        }

        echo 'ok';die;
    }

    /**
     * 把所有没有封面图的文档更换为关闭和转换事变
     */
    public function nopic() {
        $status = $this->_request('status');
        $num = $this->_request('num');
        if (!$num) {
            $num = '0,1000';
        }
        $map = array();
        $map['convert_status'] = $status ? $status : 1;
        $data = D('doc_con')->where($map)->limit($num)->select();
        $test = $this->_request('test');
        if ($test == 1) {
            print_r($data);die;
        }

        if ($data) {
            foreach ($data as $k => $v) {
                if (!$v['imgurl']) {
                    $save = array();
                    $save['convert_status'] = 3;
                    $save['status'] = 0;
                    D('doc_con')->where(array('id' => $v['id']))->save($save);
                }
            }
        }

        echo 'ok';die;
    }
}
