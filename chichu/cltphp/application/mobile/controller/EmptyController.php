<?php
namespace app\mobile\controller;
use think\Db;
use think\facade\Env;
class EmptyController extends Common{
    protected  $dao,$fields;
    public function initialize()
    {
        parent::initialize();
        $this->dao = db(DBNAME);
    }
    public function index(){
        if(DBNAME=='page'){
            db(DBNAME)->where(['id'=>input('catId')])->setInc('hits');
            $info = db(DBNAME)->where(['id'=>input('catId')])->find();
            $this->assign('info',$info);
            $template = DBNAME.'/show';
            return view($template);
        }else{
            if(DBNAME=='picture'){
                $setup = db('field')->where(array('moduleid'=>3,'field'=>'group'))->value('setup');
                $setup=is_array($setup) ? $setup: string2array($setup);
                $options = explode("\n",$setup['options']);
                foreach($options as $r) {
                    $v = explode("|",$r);
                    $k = trim($v[1]);
                    $optionsarr[$k]['val'] = $v[0];
                    $optionsarr[$k]['key'] = $k;
                }
                $this->assign('options',$optionsarr);
            }
            $map['catid'] = input('catId');
            if(DBNAME=='team'){
                $donation = db('donation')->order('id desc')->paginate($this->pagesize);
                $dpage = $donation->render();
                $dlist = $donation->toArray();
                $this->assign('dlist',$dlist['data']);
                $this->assign('dpage',$dpage);
                $list = $this->dao->where($map)->order('sort asc,createtime desc')->select();
                foreach ($list as $k=>$v){
                    $list_style = explode(';',$v['title_style']);
                    $list[$k]['title_color'] =$list_style[0];
                    $list[$k]['title_weight'] =$list_style[1];
                    $title_thumb = $v['thumb'];
                    $list[$k]['title_thumb'] = $title_thumb?$title_thumb:'/static/home/images/portfolio-thumb/p'.($k+1).'.jpg';
                }
                $this->assign('list',$list);
            }else{
                $list=$this->dao->alias('a')
                    ->join(config('database.prefix').'category c','a.catid = c.id','left')
                    ->where($map)
                    ->field('a.*,c.catdir')
                    ->order('sort asc,createtime desc')
                    ->paginate($this->pagesize);
                // 获取分页显示
                $page = $list->render();
                $list = $list->toArray();
                foreach ($list['data'] as $k=>$v){
                    $list['data'][$k]['controller'] = $v['catdir'];
                    if(isset($v['thumb'])){
                        $list['data'][$k]['title_thumb'] =imgUrl($v['thumb'],'/static/home/images/portfolio-thumb/p'.($k+1).'.jpg');
                    }else{
                        $list['data'][$k]['title_thumb'] ='/static/home/images/portfolio-thumb/p'.($k+1).'.jpg';
                    }
                }
                $this->assign('list',$list['data']);
                $this->assign('page',$page);
            }
			$template = DBNAME.'/list';
            return view($template);
        }
    }
    public function info(){
        $this->dao->where('id',input('id'))->setInc('hits');
        $info = $this->dao->where('id',input('id'))->find();
        $info['pic'] =  isset($info['pic'])?$info['pic']:"/static/home/images/sample-images/blog-post".rand(1,3).".jpg";
        $title_style = explode(';',$info['title_style']);
        $info['title_color'] = $title_style[0];
        $info['title_weight'] = $title_style[1];
        $title_thumb = $info['thumb'];
        $info['title_thumb'] = $title_thumb?$title_thumb:'/static/home/images/sample-images/blog-post'.rand(1,3).'.jpg';
        if(DBNAME=='picture'){
            $pics = explode(':::',$info['pics']);
            foreach ($pics as $k=>$v){
                $info['pics'][$k] = explode('|',$v);
            }
        }
        $this->assign('info',$info);
        $template = DBNAME.'/show';
        return view($template);
    }
    public function senMsg(){
        $data = input('post.');
        $data['addtime'] = time();
        $data['ip'] = getIp();
        db('message')->insert($data);
        $result['status'] = 1;
        return $result;
    }
    public function down($id=''){
        $map['id'] = $id;
        $files = Db::name('download')->where($map)->find();
        return download(Env::get('root_path').'public'.$files['files'], $files['title']);
    }
}