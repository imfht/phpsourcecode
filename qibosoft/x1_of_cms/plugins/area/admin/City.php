<?php
namespace plugins\area\admin;
use app\common\controller\AdminBase; 
use plugins\area\model\Area AS AreaModel;
use app\common\util\Tabel;
use app\common\util\Form;

class City extends AdminBase
{
    protected $validate = '';
    protected $cfg_level = 2;               //当前属于第几级
    protected $cfg_flevel = 1;              //父类所属第几级
    protected $cfg_fname = '省份';        //父级名称
    protected $cfg_name = '城市';         //本级名称
    protected $cfg_sfile = 'zone';           //子级文件名
    
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new AreaModel();
    }
    
	public function index($pid=0){
	    $ids = $map = [];
	    
	    if($pid){
	        $map['pid'] = $pid;
	    }else{
	        $map['level'] = $this->cfg_level==1 ? ['in',[0,1]] : $this->cfg_level;
	    }
	    
	    $listdb = AreaModel::where($map)->order( $this->getOrder('list desc') )->paginate(50,false,['query'=>request()->param()]);
	    foreach ($listdb AS $rs){
	        $ids[] = $rs['pid'];
	    }
	    $array = AreaModel::where('id','in',$ids)->column('id,name');
	    $tab = [
// 	            ['id','ID','text'],
	            ['name','名称','text.edit'],
	            ['pid','所属'.$this->cfg_fname,'select',$array],
	            ['list','排序值','text.edit'],
// 	            ['right_button', '操作', 'btn'],
	    ];
	    
	    if($this->cfg_level==1){
	        unset($tab[1]);
	    }
	    
	    $table = Tabel::make($listdb,$tab)
	    ->addTopButton('add',['title'=>'添加'.$this->cfg_name,'href'=>purl('add',['pid'=>$pid])])

	    ->addTopButton('delete')
	    ->addRightButton('edit')
	    ->addRightButton('delete')	    
	    //->addPageTips('省份管理')
	    ->addOrder('id,list')
	    ->addPageTitle($this->cfg_name.'管理');
	    if($this->cfg_sfile!=null){
	        $table->addRightButton('add',['title'=>'添加区域','href'=>purl($this->cfg_sfile.'/add',['pid'=>'__id__'])]);
	        
	        $table->addRightButton('custom',['title'=>'管理下级','href'=>purl($this->cfg_sfile.'/index',['pid'=>'__id__'])]);
	    }
	    if($this->cfg_name=='省份'){
			$table->addTopButton('add',['title'=>'导入系统地址库','href'=>purl('readcity')]);
		}
        return $table::fetchs();
	}
	
	public function add($pid=0){
	    if ($this->request->isPost()) {
	        $data = $this->request->post();
	        if (!empty($this -> validate)) {   //验证数据
	            $result = $this -> validate($data, $this -> validate);
	            if (true !== $result) $this -> error($result);
	        }
	        if($this->cfg_level>1 && empty($pid)){
	            $this->error($this->cfg_fname.'不存在');
	        }
	        $detail = explode("\r\n",$data['name']);
	        $i=0;
	        foreach($detail AS $value){
	            if(empty($value)){
	                continue;	                
	            }
	            $array =['name'=>$value,'level'=>$this->cfg_level,'pid'=>$pid];
	            if(AreaModel::create($array)){
	                $i++;
	            }
	        }
	        if ($i) {
	            $this->success('成功创建 '.$i.' 个'.$this->cfg_name, purl('index',['pid'=>$pid]) );
	        } else {
	            $this->error('创建失败');
	        }
	    }
	    $array = AreaModel::getTitleList(['level'=>$this->cfg_flevel]);
	    $form = Form::make()
	    ->addTextarea('name',$this->cfg_name.'名称','同时添加多个'.$this->cfg_name.'，请每个'.$this->cfg_name.'换一行')
	    ->addPageTitle('创建'.$this->cfg_name);
	    if($this->cfg_level>1){
	        $form->addSelect('pid','所属'.$this->cfg_fname,'',$array,$pid);
	    }
	    
	    return $form::fetchs();
	}
	public function readcity(){
		$sql = @file_get_contents('https://gitee.com/qibo168/codes/mgop2ze0ayn9t6x8klhfv27/raw?blob_name=0.sql');
		if (strlen($sql)>15) {
			$result=into_sql($sql,true,0);
			 if($result){
				 $this->success('导入成功');
			 }
		}else{
			$this->error('云端地址库下载失败');
		}

	}
	public function edit($id=0){
	    $info = AreaModel::get($id);
	    if ($this->request->isPost()) {
	        $data = $this -> request -> post();	        
	        if (!empty($this -> validate)) {   //验证数据
	            $result = $this -> validate($data, $this -> validate);
	            if (true !== $result) $this -> error($result);
	        }	        
	        if (AreaModel::update($data)) {
	            $this->success('修改成功',purl('index',['pid'=>$pid]));
	        } else {
	            $this->error('修改失败');
	        }
	    }
	    $array = AreaModel::getTitleList(['level'=>$this->cfg_flevel]);
	    $form = Form::make([],$info)
	    //->setPageTips('修改省份')
	    ->addPageTitle('修改'.$this->cfg_name)	    
	    ->addText('name','名称')	    
	    ->addHidden('id',$id);
	    if($this->cfg_level>1){
	        $form->addSelect('pid','所属'.$this->cfg_fname,'',$array);
	    }
	    return $form::fetchs();
	}
	
	public function delete($ids){
	    if (empty($ids)) {
	        $this -> error('ID有误');
	    }
	    $ids = is_array($ids)?$ids:[$ids];
	    if (AreaModel::destroy($ids)) {
	        $this->success('删除成功','index');
	    } else {
	        $this->error('删除失败');
	    }
	}
}
