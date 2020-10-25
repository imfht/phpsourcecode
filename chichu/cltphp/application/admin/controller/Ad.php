<?php
namespace app\admin\controller;
use think\Db;
use think\facade\Request;
class Ad extends Common
{
    public function initialize(){
        parent::initialize();
    }
    //广告列表
    public function index(){
        if(Request::isAjax()) {
            $key = input('post.key');
            $this->assign('testkey', $key);
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list = Db::table(config('database.prefix') . 'ad')->alias('a')
                ->join(config('database.prefix') . 'adsense at', 'a.as_id = at.as_id', 'left')
                ->field('a.*,at.name as typename')
                ->where('a.title', 'like', "%" . $key . "%")
                ->order('a.sort')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            foreach ($list['data'] as $k=>$v){
                $list['data'][$k]['addtime'] = date('Y-m-d H:s',$v['addtime']);
            }
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }
    public function add(){
        if(Request::isAjax()) {
            //构建数组
            $data = Request::except('file');
            $data['addtime'] = time();
            db('ad')->insert($data);
            $result['code'] = 1;
            $result['msg'] = '广告添加成功!';
            cache('adList', NULL);
            $result['url'] = url('index');
            return $result;
        }else{
            $adtypeList=db('adsense')->order('sort')->select();
            $this->assign('adtypeList',json_encode($adtypeList,true));

            $this->assign('title',lang('add').lang('ad'));
            $this->assign('info','null');
            $this->assign('selected', 'null');
            return $this->fetch('form');
        }
    }
    public function edit(){
        if(Request::isAjax()) {
            $data = Request::except('file');
            db('ad')->update($data);
            $result['code'] = 1;
            $result['msg'] = '广告修改成功!';
            cache('adList', NULL);
            $result['url'] = url('index');
            return $result;
        }else{
            $adtypeList=db('adsense')->order('sort')->select();
            $id=input('id');
            $adInfo=db('ad')->where(array('id'=>$id))->find();
            $this->assign('adtypeList',json_encode($adtypeList,true));

            $selected = db('adsense')->where('as_id',$adInfo['as_id'])->find();
            $this->assign('selected',json_encode($selected,true));

            $this->assign('info',json_encode($adInfo,true));
            $this->assign('title',lang('edit').lang('ad'));
            return $this->fetch('form');
        }
    }
    //设置广告状态
    public function editState(){
        $id=input('post.id');
        $open=input('post.open');
        if(db('ad')->where('id='.$id)->update(['open'=>$open])!==false){
            return ['status'=>1,'msg'=>'设置成功!'];
        }else{
            return ['status'=>0,'msg'=>'设置失败!'];
        }
    }
    public function adOrder(){
        $ad=db('ad');
        $data = input('post.');
        if($ad->update($data)!==false){
            cache('adList', NULL);
            return $result = ['msg' => '操作成功！','url'=>url('index'), 'code' =>1];
        }else{
            return $result = ['code'=>0,'msg'=>'操作失败！'];
        }
    }
    public function del(){
        db('ad')->where(array('id'=>input('id')))->delete();
        cache('adList', NULL);
        return ['code'=>1,'msg'=>'删除成功！'];
    }
    public function delall(){
        $map[] =array('id','in',input('param.ids/a'));
        db('ad')->where($map)->delete();
        cache('adList', NULL);
        $result['msg'] = '删除成功！';
        $result['code'] = 1;
        $result['url'] = url('index');
        return $result;
    }

    /***************************位置*****************************/
    //位置
    public function type(){
        if(Request::isAjax()) {
            $key = input('key');
            $this->assign('testkey', $key);
            $list = db('adsense')->where('name', 'like', "%" . $key . "%")->order('sort')->select();
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list,'rel'=>1];
        }
        return $this->fetch();
    }
    public function typeOrder(){
        $ad_type=db('adsense');
        $data = input('post.');
        if($ad_type->update($data)!==false){
            return $result = ['msg' => '操作成功！','url'=>url('type'), 'code' =>1];
        }else{
            return $result = ['code'=>0,'msg'=>'操作失败！'];
        }
    }
    public function addType(){
        if(Request::isAjax()) {
            db('adsense')->insert(input('post.'));
            $result['code'] = 1;
            $result['msg'] = '广告位保存成功!';
            $result['url'] = url('type');
            return $result;
        }else{
            $this->assign('title',lang('add').lang('ad').'位');
            $this->assign('info','null');
            return $this->fetch('typeForm');
        }
    }
    public function editType(){
        if(Request::isAjax()) {
            db('adsense')->update(input('post.'));
            $result['code'] = 1;
            $result['msg'] = '广告位修改成功!';
            $result['url'] = url('type');
            return $result;
        }else{
            $as_id=input('param.as_id');
            $info=db('adsense')->where('as_id',$as_id)->find();
            $this->assign('title',lang('edit').lang('ad').'位');
            $this->assign('info',json_encode($info,true));
            return $this->fetch('typeForm');
        }
    }
    public function delType(){
        $map['as_id'] = input('param.as_id');
        db('adsense')->where($map)->delete();//删除广告位
        db('ad')->where($map)->delete();//删除该广告位所有广告
        return ['code'=>1,'msg'=>'删除成功！'];
    }
}