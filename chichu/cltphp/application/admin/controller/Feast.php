<?php
namespace app\admin\controller;
use think\Db;
use think\Request;
use think\Controller;
class Feast extends Common
{
    public function index(){
        if(request()->isPost()) {
            $key = input('post.key');
            $this->assign('testkey', $key);
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list = Db::name('feast')
                ->where('title', 'like', "%" . $key . "%")
                ->order('sort')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            foreach ($list['data'] as $k=>$v){
                $list['data'][$k]['addtime'] = date('Y-m-d H:i',$v['addtime']);
                $list['data'][$k]['type'] = $v['type']==1?'阳历':'农历';
            }
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }
    public function add(){
        if(request()->isPost()) {
            //构建数组
            $data = input('post.');
            $data['addtime'] = time();
            db('feast')->insert($data);
            return ['code'=>1,'msg'=>'节日添加成功！','url'=>url('index')];
        }else{
            $this->assign('title',lang('add').'节日');
            $this->assign('info','null');
            return $this->fetch('form');
        }
    }
    public function edit(){
        if(request()->isPost()) {
            $data = input('post.');
            if(db('feast')->update($data)!==false){
                return ['code'=>1,'msg'=>'节日修改成功！','url'=>url('index')];
            }else{
                return ['code'=>0,'msg'=>'节日修改失败！'];
            }
        }else{
            $id=input('id');
            $adInfo=db('feast')->where(array('id'=>$id))->find();
            $this->assign('info',json_encode($adInfo,true));
            $this->assign('title',lang('edit').'节日');
            return $this->fetch('form');
        }
    }
    //设置节日状态
    public function editState(){
        $id=input('post.id');
        $open=input('post.open');
        if(db('feast')->where('id='.$id)->update(['open'=>$open])!==false){
            return ['status'=>1,'msg'=>'设置成功!'];
        }else{
            return ['status'=>0,'msg'=>'设置失败!'];
        }
    }
    public function feastOrder(){
        $feast=db('feast');
        $data = input('post.');
        if($feast->update($data)!==false){
            return $result = ['msg' => '操作成功！','url'=>url('index'), 'code' =>1];
        }else{
            return $result = ['code'=>0,'msg'=>'操作失败！'];
        }
    }
    public function del(){
        db('feast')->where(array('id'=>input('id')))->delete();
        return ['code'=>1,'msg'=>'删除成功！'];
    }
    /***********************节日元素列表****************************************************************/
    public function element(){
        if(request()->isPost()) {
            $key = input('post.key');
            $this->assign('testkey', $key);

            $page =input('page')?input('page'):1;
            $pid = input('pid');
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list = Db::name('feast_element')
                ->where([['title','like', "%" . $key . "%"],['pid','=',$pid]])
                ->order('sort')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            foreach ($list['data'] as $k=>$v){
                $list['data'][$k]['addtime'] = date('Y-m-d H:i',$v['addtime']);
            }
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }
    public function add_element(){
        if(request()->isPost()) {
            //构建数组
            $data = input('post.');
            $data['addtime'] = time();
            $data['pid'] = input('pid');
            db('feast_element')->insert($data);
            return ['code'=>1,'msg'=>'节日元素添加成功！','url'=>url('element',array('pid'=>input('pid')))];
        }else{
            $this->assign('title',lang('add').'节日元素');
            $this->assign('info','null');
            return $this->fetch('element_form');
        }
    }
    public function edit_element(){
        if(request()->isPost()) {
            $data = input('post.');
            if(db('feast_element')->update($data)!==false){
                return ['code'=>1,'msg'=>'节日元素修改成功！','url'=>url('element',array('pid'=>input('pid')))];
            }else{
                return ['code'=>0,'msg'=>'节日元素修改失败！'];
            }
        }else{
            $id=input('id');
            $adInfo=db('feast_element')->where(array('id'=>$id))->find();
            $this->assign('info',json_encode($adInfo,true));
            $this->assign('title',lang('edit').'节日元素');
            return $this->fetch('element_form');
        }
    }
    //设置节日元素状态
    public function elementState(){
        $id=input('post.id');
        $open=input('post.open');
        if(db('feast_element')->where('id='.$id)->update(['open'=>$open])!==false){
            return ['status'=>1,'msg'=>'设置成功!'];
        }else{
            return ['status'=>0,'msg'=>'设置失败!'];
        }
    }
    public function elementOrder(){
        $data = input('post.');
        if(db('feast_element')->update($data)!==false){
            return $result = ['msg' => '操作成功！', 'code' =>1];
        }else{
            return $result = ['code'=>0,'msg'=>'操作失败！'];
        }
    }
    public function delElement(){
        db('feast_element')->where(array('id'=>input('id')))->delete();
        return ['code'=>1,'msg'=>'删除成功！'];
    }

}