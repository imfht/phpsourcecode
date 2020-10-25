<?php
class toolAction extends docbaseAction {
    public function _initialize() {
        parent::_initialize();
        global $userinfo;
        $userinfo = $this->visitor->info;
        $this->assign('uid', $userinfo['uid']);
        $this->_mod = D('doc_con');
        $this->_cate_mod = D('doc_cate');
        $action = $this->_get('action', 'trim');
        $this->assign('action', $action);

        
        # add by rabin 引入convert
        import("@.ORG.convert.Core");
        $this->convert = new ORG\Convert\Core();
    }

    public function login() {
        $url = $this->_get('url');
        if ($url == "www.yag6.com" OR $url == "doc.ajie.cn") {
            echo 1;
        }else{
            echo 0;
        }
        
    }

    // 获取一级分类
    public function cate() {
        $cate = $this->_cate_mod->where(array('pid' => 0, 'status' => 1))->order('ordid')->select();
        foreach ($cate as $key => $value) {
            $mapcate['pid'] = array('eq', $value['id']);
            $mapcate['status'] = 1;
            $cate[$key]['tcate'] = D('doc_cate')->where($mapcate)->order('ordid')->select();
        }
        echo json_encode($cate);  
    }
     
    //TOOL上传
    public function updoc() {
        
        $tool = $this->_get();//接受TOOL发来的数据
        $paths = C('wkcms_attach_path');
        if (!empty($_FILES['Filedata'])) {
            $result = $this->_upload($_FILES['Filedata'], 'doc_con/');
            if ($result['error']) {
                $data['status'] = 0;
                $data['info'] = $result['info'];
            }else {

                // 组织数据
                $ins['fileurl'] =   $result['info'][0]['savename'];
                $ins['ext'] =       $result['info'][0]['extension'];
                $ins['filesize'] =  $result['info'][0]['size'];
                $ins['oldname'] =   $tool['title'];
                $ins['hash'] =      $result['info'][0]['hash'];
                $ins['cateid'] =    $tool['cateid'];
                $ins['title'] =     $tool['title'];
                $ins['score'] =     $tool['score'];
                $ins['tags'] =      $tool['tags'];
                $ins['intro'] =     $tool['intro'];
                $ins['uid'] =       $tool['uid'];
                $ins['tid'] =       $tool['tid'];
                $ins['status'] = C('wkcms_web_switch.doc_con');
                //如果文件hash已在库，则删除已上传文档
                if (D('doc_con')->hash($result['info'][0]['hash'])) {
                    @unlink($paths . 'doc_con/' . $result['info'][0]['savename']);
                    $$this->ajaxReturn(0, "文档已存在");
                }


                //处理数据
                if ($ins['tags'] == ' ' OR $ins['tags'] == '') {
                    $ins['tags'] = getcatename('doc', $ins['cateid']);
                } else {
                    $ins['tags'] = getcatename('doc', $ins['cateid']);
                }
                $tagarr = explode(',', $ins['tags']);
                $tagarr = array_unique($tagarr);
                $ins['tags'] = implode(',', $tagarr);

                if ($ins['oldname'] == '') {
                    $this->ajaxReturn(0, "没找到文件");
                }
                if ($ins['cateid'] < 1) {
                    @unlink($paths . 'doc_con/' . $result['info'][0]['savename']);
                    $this->ajaxReturn(0, "请选择分类");
                }
                if ($ins['uid'] == ' ' OR $ins['uid'] == '') {
                    @unlink($paths . 'doc_con/' . $result['info'][0]['savename']);
                    $this->ajaxReturn(0, "请设置用户ID");
                }
                if (D('doc_con')->title_exists($ins['title'])) {
                    @unlink($paths . 'doc_con/' . $result['info'][0]['savename']);
                    $this->ajaxReturn(0, "标题已经存在");
                }

                //插入
                $mod = D('doc_con');
                if (false === $ins = $mod->create($ins)) {
                    $this->ajaxReturn(0, $mod->getError());
                }
                //处理filename
                $fileinfo = explode('.', $ins['fileurl']);
                $ins['filename'] = $fileinfo[0];
                //转换处理
                if ($topicid = $mod->add($ins)) {
                    $ins['id'] = $topicid;
                    $key = $this->convert->upload($ins);
                    
                    if ($key) {
                        $save = array();
                        $save['convert_key'] = $key['data']['file'];
                        D('doc_con')->where(array('id' => $topicid))->save($save);
                    }
                    //插入tags
                $this->singletags($tool['tags']);
                $this->ajaxReturn(1, '上传成功');

                }else {
                    $this->ajaxReturn(0, '上传失败');
                }

            }

        }
        $this->ajaxReturn(0, '找不到文件');


    }

     

    
     
    public function singletags($tags){
        
        
        $tagarr=explode(',', $tags);
        
        
        foreach ($tagarr as $key =>$value){
        
            if(D('tag')->name_exists($value)){
                
                $map['name']=$value;
                D('tag')->where($map)->setInc('count',1);
            }else{
                $data['name']=$value;
                
                D('tag')->add($data);
                
            }
            
        }
        
        
        
        
    }
    
     

     

     
     

    
    /**
     * ajax获取标签
     */
    public function ajax_gettags() {
        $title = $this->_get('title', 'trim');
        if ($title) {
            $tags = D('tag')->get_tags_by_title($title);
            $tags = implode(',', $tags);
            $this->ajaxReturn(1, L('operation_success'), $tags);
        } else {
            $this->ajaxReturn(0, L('operation_failure'));
        }
    }

    
  

   
    
     
}
