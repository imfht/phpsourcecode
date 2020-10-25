<?php

namespace app\admin\controller;

use app\admin\controller\Admin;
use app\admin\builder\AdminConfigBuilder;
use app\admin\builder\AdminListBuilder;
use think\Db;

class Adv extends Admin
{

    public function pos($r = 20)
    {
        $aModule = input('module', '', 'text');
        $aTheme = input('theme', 'all', 'text');
        $aStatus = input('status', 1, 'intval');
        $_GET['status'] = $aStatus;
        if ($aModule == '') {
            $this->posModule();
            return;
        }

        $adminList = new AdminListBuilder();

        $map['path'] = array('like', ucfirst($aModule . '/%'));
        $map['status'] = $aStatus;
        if ($aTheme == 'all' || $aTheme == '') {

        } else {
            $map['theme'] = array('like', array("%,$aTheme", "%,$aTheme,%", "$aTheme,%"));

        }
        $advPosModel = model('AdvPos');
        $advModel = model('Adv');
        $advPoses = $advPosModel->where($map)->select();

        foreach ($advPoses as &$v) {
            switch ($v['type']) {
                case 1:
                    $v['type_html'] = '<span class="text-danger">单图</span>';
                    break;
                case 2:
                    $v['type_html'] = '<span class="text-warning">多图轮播</span>';
                    break;
                case 3:
                    $v['type_html'] = '<span class="text-success">文字链接</span>';
                    break;
                case 4:
                    $v['type_html'] = '<span class="text-error">代码块</span>';
                    break;
                case 0:
                    $v['type_html'] = '<span class="text-error">其它</span>';
            }
            if ($v['theme'] != 'all') {
                $theme_names = explode(',', $v['theme']);
                foreach ($theme_names as $t) {
                    $temp_theme[] = $themes[$t]['title'];
                }
                $v['theme_html'] = implode('&nbsp;&nbsp;,&nbsp;&nbsp;', $temp_theme);
                //implode(',',array_map(array($this,'getValue'),$themes,$v['theme']));//   $themes[$v['theme']]['title'];
            } else {
                $v['theme_html'] = '全部主题';
            }

            $count = $advModel->where(array('pos_id' => $v['id'], 'status' => 1))->count();
            $v['do'] = '<a href="' . url('editPos?copy=' . $v['id']) . '"><i class="icon-copy"></i> 复制</a>&nbsp;&nbsp;&nbsp;&nbsp;'
                . '<a href="' . url('editPos?module='.$aModule.'&id=' . $v['id']) . '"><i class="icon-cog"></i> 设置</a>&nbsp;&nbsp;&nbsp;&nbsp;'
                . '<a href="' . url('adv?pos_id=' . $v['id']) . '" ><i class="icon-sitemap"></i> 管理广告(' . $count . ')</a>&nbsp;&nbsp;&nbsp;&nbsp;'
                . '<a href="' . url('editAdv?pos_id=' . $v['id']) . '"><i class="icon-plus"></i> 添加广告</a>&nbsp;&nbsp;&nbsp;&nbsp;'
                . '<a href="' . url($v['path']) . '#adv_' . $v['id'] . '" target="_blank"><i class="icon-share-alt"></i>到前台查看</a>&nbsp;&nbsp;';
        }
        unset($v);

        $adminList->title('广告位管理');
        $adminList->buttonNew(url('editPos'), '添加广告位');
        $adminList->buttonDelete(url('setPosStatus'));
        $adminList->buttonDisable(url('setPosStatus'));
        $adminList->buttonEnable(url('setPosStatus'));
        $adminList
        ->keyId()
        ->keyTitle()
        ->keyHtml('do', '操作', '320px')
        ->keyText('name', '广告位英文名')
        ->keyText('path', '路径')
        ->keyHtml('type_html', '广告类型')
        ->keyStatus()
        ->keyText('width', '宽度')
        ->keyText('height', '高度')
        ->keyText('margin', '边缘留白')
        ->keyText('padding', '内部留白')
        ->keyText('theme_html', '适用主题');

        $status_array = [
            ['id' => 1, 'value' => '正常'], 
            ['id' => 0, 'value' => '禁用'], 
            ['id' => -1, 'value' => '已删除']
        ];
        $adminList->select('状态：', 'status', 'select', '广告位状态', '', '', $status_array);
        $adminList->data($advPoses);
        $adminList->display();
    }

    private static function getValue($array, $index, $col = 'title')
    {
        return $array[$index][$col];
    }

    private function posModule()
    {
        $module = model('common/Module')->getAll(1);
        $advPosModel = model('AdvPos');
        foreach ($module as $key => &$v) {
            $v['count'] = $advPosModel->where(['status' => 1, 'path' => ['like', ucfirst($v['name']) . '/%']])->count();
            $v['count'] = $v['count'] == 0 ? $v['count'] : '<strong class="text-danger" style="font-size:18px">' . $v['count'] . '</strong>';
            $v['alias_html'] = '<a href="' . Url('pos?module=' . $v['name']) . '">' . $v['alias'] . '</a>';
            $v['do'] = '<a href="' . Url('pos?module=' . $v['name']) . '"><i class="icon-sitemap"></i>' . '管理内部广告位' . '</a>';
        }


        $adminList = new AdminListBuilder();
        $adminList->data($module);
        $adminList->title('广告位管理 - 按模块选择');
        $adminList
            ->keyhtml('alias_html', '模块名')
            ->keyHtml('do', '操作')
            ->keyHtml('count', '模块内广告位数量');

        $adminList->display();
    }

    public function setPosStatus()
    {
        $aIds = input('ids', '', 'intval');
        $aStatus = input('get.status', '1', 'intval');
        $advPosModel = model('common/AdvPos');
        $map['id'] = ['in', implode(',', $aIds)];
        $result = $advPosModel->where($map)->setField('status', $aStatus);
        Db::name('Adv')->where(['pos_id' => ['in', implode(',', $aIds)]])->setField('status', $aStatus);
        if ($result === false) {
            $this->error('设置状态失败。');
        } else {
            $this->success('设置状态成功。影响了' . $result . '条数据。');
        }
    }

    public function editPos()
    {
        $aId = input('id', 0, 'intval');
        $aModule = input('get.module','','text');
        $aCopy = input('copy', 0, 'intval');
        $advPosModel = model('common/AdvPos');
        if (request()->isPost()) {
            //是提交

            $pos['name'] = input('name', '', 'text');
            $pos['title'] = input('title', '', 'text');
            $pos['path'] = input('path', '', 'text');
            $pos['type'] = input('type', 1, 'intval');
            $pos['status'] = input('status', 1, 'intval');
            $pos['width'] = input('width', '', 'text');
            $pos['height'] = input('height', '', 'text');
            $pos['margin'] = input('margin', '', 'text');
            $pos['padding'] = input('padding', '', 'text');
            $pos['theme'] = input('theme', 'all', 'text');
            switch ($pos['type']) {
                case 2:
                //todo 多图
                $pos['data'] = json_encode(array('style' => input('style', 1, 'intval')));
            }
            //新增
            if ($aId == 0) {
                $result = $advPosModel->save($pos);
            } else {
            //编辑
                $pos['id'] = $aId;
                $result = $advPosModel->save($pos,$aId);
            }

            if ($result === false) {
                $this->error('保存失败。');
            } else {
                cache('adv_pos_by_pos_' . $pos['path'] . $pos['name'], null);
                $this->success('保存成功。',url('pos',['module'=>$aModule]));
            }

        } else {
            $builder = new AdminConfigBuilder();

            if ($aCopy != 0) {
                $pos = $advPosModel->find($aCopy)->toArray();
                unset($pos['id']);
                $pos['name'] .= '   请重新设置!';
                $pos['title'] .= '   请重新设置!';
            } else {
                $pos = $advPosModel->find($aId);
                if(!empty($pos)) $pos = $pos->toArray();
            }
            if ($aId == 0) {

                if ($aCopy != 0) {
                    $builder->title('复制广告位——' . $pos['title']);
                } else {
                    $builder->title('新增广告位');
                }
            } else {
                $builder->title($pos['title'] . '【' . $pos['name'] . '】' . ' 设置——' . $advPosModel->switchType($pos['type']));
            }
            
            //
            $builder->keyId()
                    ->keyTitle('title','广告名')
                    ->keyText('name', '标识', '字母和数字构成，同一个页面上不要出现两个同名的')
                    ->keyText('path', '路径', '模块名/控制器名/方法名，例如：Home/Index/detail')
                    ->keyRadio('type', '广告类型', '', array(1 => '单图广告', 2 => '多图轮播', 3 => '文字链接', 4 => '代码'))
                    ->keyText('width', '宽度', '支持各类长度单位，如px，em，%')
                    ->keyText('height', '高度', '支持各类长度单位，如px，em，%')
                    ->keyText('margin', '边缘留白', '支持各类长度单位，如px，em，%；依次为：上  右  下  左，如 5px 2px 0 3px')
                    ->keyText('padding', '内部留白', '支持各类长度单位，如px，em，%；依次为：上  右  下  左，如 5px 2px 0 3px');
            $data = json_decode($pos['data'], true);

            if (!empty($data)) {
                $pos = array_merge($pos, $data);
            }

            if ($pos['type'] == 2) {

                $builder
                ->keyRadio('style', '轮播_风格', '', [1 => 'Swiper 风格', 2 => 'KinmaxShow 风格', 3 =>'Flickity 风格'])
                ->keyDefault('style', 1);
            }


            $builder->keyDefault('type', 1)
                    ->keyDefault('status', 1);
                    //->keyStatus('status','状态',1);
            $builder->data($pos);
            $builder->buttonSubmit()
                    ->buttonBack()
                    ->display();
        }
    }
    /**
     * 广告列表
     * @return [type] [description]
     */
    public function adv()
    {
        $aPosId = input('pos_id', 0, 'intval');
        if(!$aPosId){
            $this->error('未传入"pos_id"参数');
        }
        //$advPosModel = model('common/AdvPos');
        $pos = Db::name('adv_pos')->find($aPosId);
        if ($aPosId != 0) {
            $map['pos_id'] = $aPosId;
        }
        $data = Db::name('adv')->where($map)->order('pos_id desc,sort desc')->select();

        //todo 广告管理列表
        $builder = new AdminListBuilder();
        if ($aPosId == 0) {
            $builder->title('广告管理');
        } else {
            $builder->title($pos['title'] . '【' . $pos['name'] . '】' . ' 设置——' . model('AdvPos')->switchType($pos['type']));
        }
        $builder->keyId();
        $builder->keyText('title', '广告');
        
        $builder->keyText('click_count', '点击量');
        $builder->buttonNew(url('editAdv',['pos_id'=>$aPosId]), '新增/编辑广告');
        $builder->buttonDelete(url('setDel',['pos_id'=>$aPosId]), '删除');
        if ($aPosId != 0) {
            $builder->button('广告排期查看', ['href' => url('schedule?pos_id=' . $aPosId)]);
            $builder->button('设置广告位', ['href' => url('editPos?id=' . $aPosId)]);
        }
        $builder->keyText('url', '链接地址')
                ->keyTime('start_time', '开始生效时间', '不设置则立即生效')
                ->keyTime('end_time', '失效时间', '不设置则一直有效')
                ->keyText('sort', '排序')
                ->keyCreateTime()
                ->keyStatus();
        $builder->keyLink('', '预览', 'adv_info?id=###');
        $builder->keyDoActionEdit("editAdv?id=###", '编辑');
        $builder->data($data);     
        
        $builder->display();
    }

    public function schedule()
    {
        $aPosId = input('pos_id', 0, 'intval');
        if ($aPosId != 0) {
            $map['pos_id'] = $aPosId;
        }
        $map['status'] = 1;
        $data = Db::name('Adv')->where($map)->select();
        foreach ($data as $v) {
            $events[] = array('title' => '<strong>' . $v['title'] . '</strong>', 'start' => date('Y-m-d h:i', $v['start_time']), 'end' => date('Y-m-d h:i', $v['end_time']), 'data' => array('id' => $v['id']));
        }
        $this->assign('events', json_encode($events));
        $this->assign('pos_id', $aPosId);
        return $this->fetch();
    }
    /**
     * 编辑广告
     * @return [type] [description]
     */
    public function editAdv()
    {
        $aId = input('id', 0, 'intval');
        if ($aId != 0) {
            $adv = Db::name('adv')->where(['status'=>1])->find($aId);
            $aPosId = $adv['pos_id'];
        } else {
            $aPosId = input('pos_id', 0, 'intval');
        }

        $pos = Db::name('AdvPos')->where(['id' => $aPosId])->find();

        if (request()->isPost()) {
            $inputData = input('post.');
            //dump($inputData);exit;

            cache('adv_list_' . $pos['name'] . $pos['path'], null);
            if ($pos['type'] == 2) {
                //todo 多图
                $aPosId = $inputData['pos_id'];
                $aTitles = $inputData['title'];
                $aDescription = $inputData['description'];
                $aUrl = $inputData['url'];
                $aSort = $inputData['sort'];
                $aStartTime = $inputData['start_time'];
                $aEndTime = $inputData['end_time'];
                $aTarget = $inputData['target'];
                $added = 0;
                Db::name('adv')->where(['pos_id' => $aPosId])->delete();
                //获取现有广告位所有广告
                $allThisAdv = Db::name('adv')->where(['pos_id' => $aPosId])->select();
                foreach ($inputData['pic'] as $key => $v) {
                    $data['pic'] = $v;
                    $data['target'] = $aTarget[$key];
                    $adv_temp['title'] = $aTitles[$key];
                    $adv_temp['pos_id'] = $aPosId;
                    $adv_temp['url'] = $aUrl[$key];
                    $adv_temp['sort'] = $aSort[$key];
                    $adv_temp['status'] = 1;
                    $adv_temp['create_time'] = time();
                    $adv_temp['start_time'] = $aStartTime[$key];
                    $adv_temp['end_time'] = $aEndTime[$key];
                    $adv_temp['target'] = $aTarget[$key];
                    $adv_temp['data'] = json_encode($data);
                    //todo 处理数据库数据
                    $result = Db::name('adv')->insert($adv_temp);
                    
                    if ($result !== false) {
                        $added++;
                    }
                }
                $this->success('成功改动' . $added . '个广告。',url('adv',['pos_id'=>$aPosId]));

            } else {
                switch ($pos['type']) {
                    case 1:
                        //todo 单图
                        $data['pic'] = $inputData['pic'];
                        $data['target'] = $inputData['target'];
                        break;
                    case 3:
                        //todo 文本
                        $data['text'] = $inputData['text'];
                        $data['text_color'] = $inputData['text_color'];
                        $data['text_font_size'] = $inputData['text_font_size'];
                        $data['target'] = $inputData['target'];
                        //todo 文字
                        break;
                    case 4:
                        //todo 代码
                        $data['code'] = $inputData['code'];
                        break;
                }
                $adv['title'] = $inputData['title'];
                $adv['pos_id'] = $aPosId;
                //$adv['description'] = $inputData['description'];
                $adv['url'] = $inputData['url'];
                $adv['sort'] = $inputData['sort'];
                $adv['create_time'] = time();
                $adv['start_time'] = $inputData['start_time'];
                $adv['end_time'] = $inputData['end_time'];
                $adv['data'] = json_encode($data);

                if ($aId == 0) {
                    $result = Db::name('adv')->insert($adv);
                } else {
                    $adv['id'] = $aId;
                    $result = Db::name('adv')->update($adv);
                }

                if ($result === false) {
                    $this->error('保存失败');
                } else {
                    $this->success('保存成功',url('adv',['pos_id'=>$aPosId]));
                }
            }
        //构造页面
        } else {
            //快速添加广告位逻辑
            //todo 快速添加
            $builder = new AdminConfigBuilder();
            //$adv['pos'] = '广告名：' .$pos['title'] . '| 标识：' . $pos['name'] . '| 路径：' . $pos['path'];
            $adv['pos'] = <<<EOT
<span class="label label-success">广告名：{$pos['title']}</span>
<span class="label label-danger">标识：{$pos['name']}</span>
<span class="label">路径：{$pos['path']}</span>
EOT;
            $adv['pos_id'] = $aPosId;
            $builder->keyReadOnlyHtml('pos', '所属广告位');
            $builder->keyReadOnly('pos_id', '广告位ID');
            $builder->keyId();
            $builder->keyTitle('title', '广告名');
            $builder->title($pos['title'] . '设置——' . model('Common/AdvPos')->switchType($pos['type']));
            $builder
                ->keyTime('start_time', '开始生效时间', '不设置则立即生效')
                ->keyTime('end_time', '失效时间', '不设置则一直有效')
                ->keyText('sort', '排序')
                ->keyCreateTime()
                ->keyStatus();

            $builder->buttonSubmit();
            if (!empty($adv['data'])) {
                $data = json_decode($adv['data'], true);
            }
            
            if (!empty($data)) {
                $adv = array_merge($adv, $data);
            }
            if ($aId) {
                $builder->data($adv);
            } else {
                $builder->data(['pos' => $adv['pos'], 'pos_id' => $aPosId]);
            }

            switch ($pos['type']) {
                case 1:
                    //todo 单图
                    $builder->keySingleImage('pic', '图片', '选图上传，建议尺寸' . $pos['width'] . '*' . $pos['height']);
                    $builder->keyText('url', '链接地址');
                    $builder->keySelect('target', '打开方式', null, array('_blank' => '新窗口:_blank', '_self' => '当前层:_self', '_parent' => '父框架:_parent', '_top' => '整个框架:_top'));
                    break;
                case 2:
                    //todo 多图
                    break;
                case 3:
                    $builder->keyText('text', '文字内容', '广告展示文字');
                    $builder->keyText('url', '链接地址');
                    $builder->keyColor('text_color', '文字颜色', '文字颜色')->keyDefault('data[text_color]', '#000000');
                    $builder->keyText('text_font_size', '文字大小，需带单位，例如：14px')->keyDefault('data[text_font_size]', '12px');
                    $builder->keySelect('target', '打开方式', null, array('_blank' => '新窗口:_blank', '_self' => '当前层:_self', '_parent' => '父框架:_parent', '_top' => '整个框架:_top'));

                    //todo 文字
                    break;
                case 4:
                    //todo 代码
                    $builder->keyTextArea('code', '代码内容', '不对此字段进行过滤，可填写js、html');
                    break;
            }

            $builder
                ->keyDefault('status', 1)->keyDefault('sort', 1)
                ->keyDefault('title', $pos['title'] . '的广告 ' . date('m月d日', time()) . ' 添加')
                ->keyDefault('end_time', time() + 60 * 60 * 24 * 7);

            if ($pos['type'] == 2) {
                $meta_title = $pos['title'] . '设置——' . model('Common/AdvPos')->switchType($pos['type']);
                $adv['start_time'] = isset($adv['start_time']) ? $adv['start_time'] : time();
                $adv['end_time'] = isset($adv['end_time']) ? $adv['end_time'] : time() + 60 * 60 * 24 * 7;
                $adv['create_time'] = isset($adv['create_time']) ? $adv['create_time'] : time();
                $adv['sort'] = isset($adv['sort']) ? $adv['sort'] : 1;
                $adv['status'] = isset($adv['status']) ? $adv['status'] : 1;

                $advs = Db::name('Adv')->where(['pos_id' => $aPosId])->select();
                foreach ($advs as &$v) {
                    $data = json_decode($v['data'], true);
                    if (!empty($data)) {
                        $v = array_merge($v, $data);
                    }
                }
                unset($v);
                $this->assign('meta_title',$meta_title);
                $this->assign('list', $advs);
                $this->assign('pos', $pos);
                return $this->fetch('editslider');
            } else {
                $builder->display();
            }
        }
    }
    /**
     * 广告预览
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function adv_info($id){
        
        $data = Db::name('adv')->where(['id'=>$id])->find();
        $data['data'] = json_decode($data['data'],true);

        if(isset($data['data']['pic'])){
            $data['data']['pic_url'] = pic($data['data']['pic']);
        }
        $data['create_time'] = date("Y-m-d H:i:s",$data['create_time']);
        $data['start_time'] = date("Y-m-d H:i:s",$data['start_time']);
        $data['end_time'] = date("Y-m-d H:i:s",$data['end_time']);

        $this->_meta_title = '广告位详情';
        $this->assign('meta_title',$this->_meta_title);
        $this->assign('data',$data);
        return $this->fetch();
    }
    /**
     * 删除广告（设置为删除状态）
     * @param [type] $ids [description]
     */
    public function setDel($ids,$pos_id)
    {
        !is_array($ids)&&$ids=explode(',',$ids);
        $res=model('common/Adv')->setDel($ids);
        if($res){
            $this->success('操作成功！',url('Adv/adv',['pos_id'=>$pos_id]));
        }else{
            $this->error('操作失败！'.model('common/Adv')->getError());
        }
    }
    /**
     * 真实删除广告
     * @param [type] $ids [description]
     */
    //真实删除，留个下个版本处理，暂时先保留该方法
    public function setTrueDel($ids,$pos_id)
    {
    if(request()->isPost()){
        $ids=input('post.ids/a','','text');
        $ids=explode(',',$ids);
        //!is_array($ids)&&$ids=explode(',',$ids);
        $res=model('common/Adv')->setTrueDel($ids);
        if($res){
            $this->success('彻底删除成功！',url('Adv/adv',['pos_id'=>$pos_id]));
        }else{
            $this->error('操作失败！'.model('common/Adv')->getError());
        }
    }else{
        $ids=input('ids');
            $ids=implode(',',$ids);
            $this->assign('ids',$ids);
            return $this->fetch();
        }
    }


}
