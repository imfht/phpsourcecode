<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\Archive as Archives;
use app\common\model\Arctype;
use app\common\model\ArctypeMod;
use think\Db;
use think\facade\Request;

class Archive extends Common
{
    private $cModel;   //当前控制器关联模型

    public function initialize()
    {
        parent::initialize();
        $this->cModel = new Archives;   //别名：避免与控制名冲突
    }

    public function index()
    {
        $where = [];
        if (input('get.search')){
            $where[] = ['title|keywords|description','like', '%'.input('get.search').'%'];
        }
        if (input('get._sort')){
            $order = explode(',', input('get._sort'));
            $order = $order[0].' '.$order[1];
        }else{
            $order = 'id desc';
        }
        $dataList = $this->cModel->where($where)->order($order)->paginate(15);
        foreach ($dataList as $k => $v){
            $addonMod = $v['mod'];
            $dataList[$k]['addondata'] = $v->$addonMod;
            unset($dataList[$k][$addonMod]);
        }
        $this->assign('dataList', $dataList);
        return $this->fetch();
    }

    public function create($typeid) {	//添加
        if (request()->isPost()){
            $data = Request::param();
            $data['create_time'] = strtotime($data['create_time']);

            if ( isset($data['flag']) ){
                $this->cModel->flag = $data['flag'];
            }else{
            	$this->cModel->flag = [];
            }
			$result = $this->validate($data,C_NAME.'.add');
			if( true !== $result ){
				// 验证失败 输出错误信息
				return ajaxReturn($result);
			}else{
            	$result = $this->cModel->allowField(true)->save($data);
            	$data['aid'] = $this->cModel->id;
				$mod = $data['mod'];
            	$addonData = db($mod)->field('id', true)->strict(false)->insert($data);   //新增关联表数据
				if( $result && $addonData ){
					$image = new \app\common\model\Image;
					if( !empty($data['imgurl']) ){
						$image->imgurl = $data['imgurl'];
						$image->save(['fid'=>$this->cModel->id]);
					}
					return ajaxReturn('操作成功', url('Arctype/index'));
				}else{
					return ajaxReturn('操作失败');
				}
			}
        }else{
            $atModel = new Arctype();
            $arctypeList = $atModel->treeList();
            $this->assign('arctypeList', $arctypeList);

            $arcData = $atModel->where(['id' => $typeid])->find();   //栏目数据
            $atmModel = new ArctypeMod();
            $where = [ 'id' => $arcData['mid'] ];
            $mod = $atmModel->where($where)->field('mod')->find();
            $mod = $mod['mod'];
            $this->assign('mods', $mod);   //文章拓展表模型
			$data = [
				'typeid'        => $arcData['id'],
				'mid'           => $arcData['mid'],
                'create_time'   => date('Y-m-d H:i:s', time()),
                'imgurl'        => [],
                'addondata'     => ['video_url'=>'']
			];
            $this->assign('data', $data);
            return $this->fetch('edit');
        }
    }

    public function edit($id) {	//编辑
        if (request()->isPost()){
            $data = Request::post();
            if (isset($data['create_time'])){
                $data['create_time'] = strtotime($data['create_time']);
            }
            if ( isset($data['flag']) ){
            	$this->cModel->flag = $data['flag'];
            }else{
            	$this->cModel->flag = [];
            }
            if (count($data) == 2){
                foreach ($data as $k =>$v){
                    $fv = $k!='id' ? $k : '';
                }
				$result = $this->validate($data,C_NAME.'.'.$fv);
				if( true !== $result ){
					return ajaxReturn($result);
				}else{
					$result = $this->cModel->allowField(true)->save($data, $data['id']);
					if ($result){
		                return ajaxReturn('操作成功', url('index'));
		            }else{
		                return ajaxReturn('操作失败');
		            }
				}
            }else{
				if( empty($data['content']) ){
					return ajaxReturn('内容不能为空');
				}
            	$result = $this->validate($data,C_NAME.'.edit');
				if( true !== $result ){
					return ajaxReturn($result);
				}else{
					$result = $this->cModel->allowField(true)->save($data, $data['id']);
					$mod = $data['mod'];
	        		$addonData = db($mod)->field('id', true)->strict(false)->where( 'aid='.$data['id'] )->update($data);   //关联表数据
				}
	        	if ($result && $addonData){
	        		$image = new \app\common\model\Image;
					if( !empty($data['imgurl']) ){
						$imgdata = $image->where(['fid'=>$data['id']])->find();
						if( empty($imgdata) ){
							$image->imgurl = $data['imgurl'];
							$image->save(['fid'=>$data['id']]);
						}else{
							$image->imgurl = $data['imgurl'];
							$image->save(['fid'=>$data['id']],['fid'=>$data['id']]);
						}
					}
	                return ajaxReturn('操作成功', url('index'));
	            }else{
	                return ajaxReturn('操作失败');
	            }
            }
        }else{
            $atModel = new Arctype();
            $arctypeList = $atModel->treeList();
            $this->assign('arctypeList', $arctypeList);

            $data = $this->cModel->get($id);
            $addonMod = $data['mod'];
            $data['addondata'] = $data->$addonMod;   //拓展表数据
            unset($data[$data['mod']]);
            $atmModel = new ArctypeMod();
            $data['mid'] = $atmModel->where(['mod' => $addonMod])->value('id');
            $this->assign('mods', $addonMod);
            $this->assign('data', $data);
            return $this->fetch();
        }
    }

    public function delete() {	//删除文章
        if (request()->isPost()){
            $id = input('id');
            if (isset($id) && !empty($id)){
                $id_arr = explode(',', $id);
                if (!empty($id_arr)){
					$arc_reply = new \app\common\model\ArchiveReply;
                    foreach ($id_arr as $val){
                        $arcdata = $this->cModel->where(['id' => $val])->find();
						$image = new \app\common\model\Image;
						$delimg = delimg($arcdata->imgurl);	//删除图片
						if( $delimg ){
							$image->where(['fid'=>$val])->delete();	//删除图片记录
						}
                        $this->cModel->where('id='.$val)->delete();	//删除文章
                        db($arcdata['mod'])->where('aid='.$val)->delete();   //删除关联表数据
                        $arc_reply_id = $arc_reply->where(['aid'=>$val])->column('id');
						$where[] = ['ar_id','in',$arc_reply_id];
						db('ZanLog')->where($where)->delete();   //删除 文章回复 赞数据
						db('ZanLog')->where(['a_id'=>$val])->delete();   //删除 文章 赞数据
						$arc_reply->where(['aid'=>$val])->delete();	//删除 文章回复
						db('Collect')->where(['aid'=>$val])->delete();   //删除 被收藏文章的记录
                    }
                    return ajaxReturn('操作成功', url('index'));
                }else{
                    return ajaxReturn('操作失败');
                }
            }
        }
    }

}