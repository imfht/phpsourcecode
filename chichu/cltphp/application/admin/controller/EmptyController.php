<?php
namespace app\admin\controller;
use think\Db;
use think\facade\Request;
use clt\Form;
use app\admin\model\Tags as Tags;
class EmptyController extends Common{
    protected  $dao,$fields;
    public function initialize()
    {
        parent::initialize();
        $this->moduleid = $this->mod[MODULE_NAME];
        $this->dao = db(MODULE_NAME);
        $fields = cache($this->moduleid.'_Field');
        foreach($fields as $key => $res){
            $res['setup']=string2array($res['setup']);
            $this->fields[$key]=$res;
        }
        unset($fields);
        unset($res);
        $this->assign ('fields',$this->fields);
    }
    public function index(){
        if(Request::isAjax()){
            $request = Request::instance();
            $modelname = strtolower($request->controller());
            $model = db($modelname);
            $keyword=input('post.key');
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $order = "sort asc,id desc";
            if (input('post.catid')) {
                $catids = db('category')->where(array('pid'=>input('post.catid')))->column('id');
                if($catids){
                    $catid = input('post.catid').','.implode(',',$catids);
                }else{
                    $catid = input('post.catid');
                }
            }
            $cinfo= db('category')->where(array('id'=>input('post.catid')))->field('catdir,is_show')->find();
            if(!empty($keyword) ){
                $map[]=array('title','like','%'.$keyword.'%');
            }
            $prefix=config('database.prefix');
            $Fields=Db::getConnection()->getFields($prefix.$modelname);
            foreach ($Fields as $k=>$v){
                $field[$k] = $k;
            }
            if(in_array('catid',$field)){
               $map[]=array('catid','in',$catid);
            }
            $list = $model
                ->where($map)
                ->order($order)
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            $rsult['code'] = 0;
            $rsult['msg'] = "获取成功";
            $lists = $list['data'];
            foreach ($lists as $k=>$v ){
                $lists[$k]['createtime'] = date('Y-m-d H:i:s',$v['createtime']);
                $lists[$k]['catdir'] =  $cinfo['catdir'];
                $lists[$k]['is_show'] =  $cinfo['is_show'];
                $lists[$k]['url'] = url('home/'.$cinfo['catdir'].'/info',['id'=>$v['id'],'catId'=>$v['catid']]);
            }
            $rsult['data'] = $lists;
            $rsult['count'] = $list['total'];
            $rsult['rel'] = 1;
            return $rsult;
        }else{
            return $this->fetch ('content/index');
        }
    }

    public function edit(){
        $id = input('id');
        $request = Request::instance();
        $controllerName = $request->controller();
        if($controllerName=='Page'){
            $p = $this->dao->where('id',$id)->find();
            if(empty($p)){
                $data['id']=$id;
                $data['title'] = $this->categorys[$id]['catname'];
                //$data['keywords'] = $this->categorys[$id]['keywords'];
                $this->dao->insert($data);
            }
        }
        $info = $this->dao->where('id',$id)->find();
        $form=new Form($info);
        $returnData['vo'] = $info;
        $returnData['form'] = $form;
        $this->assign ('info', $info );
        $this->assign ( 'form', $form );
        $this->assign ( 'title', '编辑内容' );
        return $this->fetch('content/edit');
    }
    function update(){
        $request = Request::instance();

        $controllerName = $request->controller();
        $model = $this->dao;
        $fields = $this->fields;
        $data = $this->checkfield($fields,Request::except('file'));
        if(isset($data['code'])){
            return ['code'=>0,'msg'=>$data['msg']];
        }
        if(isset($fields['updatetime'])) {
            $data['userid'] = session('aid');
        }

        if(isset($fields['updatetime'])) {
            $data['updatetime'] = time();
        }

        $title_style ='';
        if (isset($data['style_color'])) {
            $title_style .= 'color:' . $data['style_color'].';';
            unset($data['style_color']);
        }else{
            $title_style .= 'color:#222;';
        }
        if (isset($data['style_bold'])) {
            $title_style .= 'font-weight:' . $data['style_bold'].';';
            unset($data['style_bold']);
        }else{
            $title_style .= 'font-weight:normal;';
        }
        if($fields['title']['setup']['style']==1) {
            $data['title_style'] = $title_style;
        }
        if($controllerName!='Page') {
            $data['updatetime'] = time();
        }
        unset($data['aid']);
        unset($data['pics_name']);
        //编辑多图和多文件
        foreach ($fields as $k=>$v){
            if($v['type']=='files' ){
                if(!$data[$k]){
                    $data[$k]='';
                }
                $data[$v['field']] = $data['images'];
            }
            if($v['type']=='images'){
                if(!isset($data[$k])){
                    $data[$k]='';
                }
                if($data[$k]){
                    $data[$v['field']] = implode(';',$data[$k]);
                }
            }
        }
        $list=$model->strict(false)->update($data);
        if (false !== $list) {
            if($controllerName=='Page'){
                $result['url'] = url("admin/category/index");
            }else{
                $result['url'] = url("admin/".$controllerName."/index",array('catid'=>$data['catid']));
            }
            //标签
            if(isset($data['tags'])){
                $tags = array_filter(explode(',', $data['tags']));
                if ($tags) {
                    $tagsId = Db::name('article_tags')->where('article_id',$data['id'])->column('tag_id');
                    if($tagsId){
                        //如果存在，则全部删除后，重新添加
                        //统计减1
                        Tags::where('id', 'in', $tagsId)->setDec('nums');
                        //删除全部
                        Db::name('article_tags')->where('article_id',$data['id'])->delete();
                        //重新添加
                        foreach ($tags as $k => $v) {
                            $info = Tags::where('name', $v)->find();
                            if($info){
                                Tags::where('name', $v)->setInc('nums');
                                $data3['tag_id'] = $info['id'];
                            }else{
                                $data2 = ['name' => $v, 'nums' => 1];
                                $data3['tag_id'] = model('tags')->insertGetId($data2);
                            }
                            $data3['article_id'] = $data['id'];
                            Db::name('article_tags')->insert($data3);
                        }
                    }else{
                        //如果不存在
                        $tagslist = Tags::where('name', 'in', $tags)->select();
                        if(count($tagslist)>0){
                            foreach ($tagslist as $k => $v) {
                                $data3['tag_id'] = $v['id'];
                                $data3['article_id'] = $data['id'];
                                Db::name('article_tags')->insert($data3);
                                $v->nums++;
                                $v->save();
                                $tags = array_diff($tags, [$v['name']]);
                            }
                            foreach ($tags as $k => $v) {
                                $data2 = ['name' => $v, 'nums' => 1];
                                $data3['tag_id'] = model('tags')->insertGetId($data2);
                                $data3['article_id'] = $data['id'];
                                Db::name('article_tags')->insert($data3);
                            }
                        }else{
                            foreach ($tags as $k => $v) {
                                $data2 = ['name' => $v, 'nums' => 1];
                                $data3['tag_id'] = model('tags')->insertGetId($data2);
                                $data3['article_id'] = $data['id'];
                                Db::name('article_tags')->insert($data3);
                            }
                        }
                    }
                }
            }

            $result['msg'] = '修改成功!';
            $result['code'] = 1;
            return $result;
        } else {
            $result['msg'] = '修改失败!';
            $result['code'] = 0;
            return $result;
        }
    }
    public function set_categorys($categorys = array()) {
        if (is_array($categorys) && !empty($categorys)) {
            foreach ($categorys as $id => $c) {
                $this->categorys[$c['id']] = $c;
                $r = db('category')->where("pid = $c[id]")->order('listorder ASC,id ASC')->select();
                $this->set_categorys($r);
            }
        }
        return true;
    }
    function checkfield($fields,$post){
        foreach ( $post as $key => $val ) {
            if(isset($fields[$key])){
                $setup=$fields[$key]['setup'];
                if(!empty($fields[$key]['required']) && empty($post[$key])){
                    $result['msg'] = $fields[$key]['errormsg']?$fields[$key]['errormsg']:'缺少必要参数！';
                    $result['code'] = 0;
                    return $result;
                }
                if(isset($setup['multiple'])){
                    if(is_array($post[$key])){
                        $post[$key] = implode(',',$post[$key]);
                    }
                }
                if(isset($setup['inputtype'])){
                    if($setup['inputtype']=='checkbox'){
                        $post[$key] = implode(',',$post[$key]);
                    }
                }
                if(isset($setup['fieldtype'])){
                    if($fields[$key]['type']=='checkbox'){
                        $post[$key] = implode(',',$post[$key]);
                    }
                }
                if($fields[$key]['type']=='datetime'){
                    $post[$key] =strtotime($post[$key]);
                }elseif($fields[$key]['type']=='textarea'){
                    $post[$key]=addslashes($post[$key]);
                }elseif($fields[$key]['type']=='linkage'){
                    if($post[$key][0]){
                        $post[$key] = implode(',',$post[$key]);
                    }else{
                        unset($post[$key]);
                    }
                }elseif($fields[$key]['type']=='editor'){
                    $field = $fields[$key]['field'];
                    $post[$key] =htmlspecialchars_decode($post[$field]);
                    if($field == 'content'){
                        if(isset($post['description']) && $post['description'] == '' && isset($post['content'])) {
                            $content = stripslashes($post['content']);
                            $description_length = 120;
                            $post['description'] = str_cut(str_replace(array("\r\n","\t",'[page]','[/page]','&ldquo;','&rdquo;'), '', strip_tags($content)),$description_length);
                            $post['description'] = addslashes($post['description']);
                        }
                    }
                }
            }
        }
        return $post;
    }

    public function add(){
        $form=new Form();
        $this->assign ( 'form', $form );
        $this->assign ( 'title', '添加内容' );
        return $this->fetch('content/edit');
    }
    public function insert(){
        $request = Request::instance();
        $controllerName = $request->controller();
        $model = $this->dao;
        $fields = $this->fields;
        $data = $this->checkfield($fields,Request::except('file'));
        if(isset($data['code']) && $data['code']==0){
            return $data;
        }
        if(isset($fields['createtime'])  && !isset($data['createtime']) ){
            $data['createtime'] = time();
        }
        if(isset($fields['updatetime'])  && !isset($data['updatetime']) ) {
            $data['updatetime'] = time();
        }
        if($controllerName!='Page') {
            if (isset($fields['updatetime'])){
                $data['updatetime'] = $data['createtime'];
            }
        }
        $data['userid'] = session('aid');
        $data['username'] = session('username');

        $title_style ='';
        if (isset($data['style_color'])) {
            $title_style .= 'color:' . $data['style_color'].';';
            unset($data['style_color']);
        }else{
            $title_style .= 'color:#222;';
        }
        if (isset($data['style_bold'])) {
            $title_style .= 'font-weight:' . $data['style_bold'].';';
            unset($data['style_bold']);
        }else{
            $title_style .= 'font-weight:normal;';
        }
        if($fields['title']['setup']['style']==1) {
            $data['title_style'] = $title_style;
        }

        unset($data['style_color']);
        unset($data['style_bold']);
        unset($data['aid']);
        unset($data['pics_name']);
        //编辑多图和多文件
        foreach ($fields as $k=>$v){
            if($v['type']=='files' ){
                if(!$data[$k]){
                    $data[$k]='';
                }
                $data[$v['field']] = $data['images'];
            }
            if($v['type']=='images'){
                if(!isset($data[$k])){
                    $data[$k]='';
                }
                if($data[$k]){
                    $data[$v['field']] = implode(';',$data[$k]);
                }
            }
        }
        $id= $model->insertGetId($data);
        if ($id !==false) {
            if($controllerName=='page'){
                $result['url'] = url("admin/category/index");
            }else{
                $result['url'] = url("admin/".$controllerName."/index",array('catid'=>$data['catid']));
            }

            //标签
            if(isset($data['tags'])){
                $tags = array_filter(explode(',', $data['tags']));
                if ($tags) {
                    $tagslist = Tags::where('name', 'in', $tags)->select();
                    if(count($tagslist)>0){
                        foreach ($tagslist as $k => $v) {
                            $data3['tag_id'] = $v['id'];
                            $data3['article_id'] = $id;
                            Db::name('article_tags')->insert($data3);
                            $v->nums++;
                            $v->save();
                            $tags = array_diff($tags, [$v['name']]);
                        }
                        foreach ($tags as $k => $v) {
                            $data2 = ['name' => $v, 'nums' => 1];
                            $data3['tag_id'] = model('tags')->insertGetId($data2);
                            $data3['article_id'] = $id;
                            Db::name('article_tags')->insert($data3);
                        }
                    }else{
                        foreach ($tags as $k => $v) {
                            $data2 = ['name' => $v, 'nums' => 1];
                            $data3['tag_id'] = model('tags')->insertGetId($data2);
                            $data3['article_id'] = $id;
                            Db::name('article_tags')->insert($data3);
                        }
                    }
                }
            }
            $result['msg'] = '添加成功!';
            $result['code'] = 1;
            return $result;
        } else {
            $result['msg'] = '添加失败!';
            $result['code'] = 0;
            return $result;
        }

    }
    public function listDel(){
        $id = input('post.id');
        $model = $this->dao;
        $model->where(array('id'=>$id))->delete();//转入回收站
        return ['code'=>1,'msg'=>'删除成功！'];
    }
    public function delAll(){
        $map[] =array('id','in',input('post.ids/a'));
        $model = $this->dao;
        $model->where($map)->delete();
        $result['code'] = 1;
        $result['msg'] = '删除成功！';
        $result['url'] = url('index',array('catid'=>input('post.catid')));
        return $result;
    }
    public function listorder(){
        $model = $this->dao;
        $catid = input('catid');
        $data = input('post.');
        $model->update($data);
        $result = ['msg' => '排序成功！','url'=>url('index',array('catid'=>$catid)), 'code' => 1];
        return $result;
    }
    public function delImg(){
        if(!input('post.url')){
            return ['code'=>0,'请指定要删除的图片资源'];
        }
        $file = ROOT_PATH.__PUBLIC__.input('post.url');
        if(file_exists($file) && trim(input('post.url'))!=''){
            is_dir($file) ? dir_delete($file) : unlink($file);
        }
        if(input('post.id')){
            $picurl = input('post.picurl');
            $picurlArr = explode(':',$picurl);
            $pics = substr(implode(":::",$picurlArr),0,-3);
            $model = $this->dao;
            $map['id'] =input('post.id');
            $model->where($map)->update(array('pics'=>$pics));
        }
        $result['msg'] = '删除成功!';
        $result['code'] = 1;
        return $result;
    }
    public function getRegion(){
        $Region=db("region");
        $map['pid'] = input("pid");
        $list=$Region->where($map)->select();
        return $list;
    }
}