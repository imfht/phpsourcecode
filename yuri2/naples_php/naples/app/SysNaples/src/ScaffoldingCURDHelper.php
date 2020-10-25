<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2017/2/20
 * Time: 21:14
 */

namespace naples\app\SysNaples\src;

/** 
 * 脚手架辅助类 帮助建设CURD的代码
 */
class ScaffoldingCURDHelper
{
    private $errMsg='';
    private $randomID='';
    private $data=[]; //主存储数组
    private $ctrlName; //控制器名
    private $moduleName; //模块名
    private $isSafe='safe'; //是否安全覆盖
    private $txtFile=''; //保存的txt文件内容
    private $ctrlFilePath; //控制器文件路径
    private $viewListFilePath; //列表视图文件路径
    private $viewDetailFilePath; //详情视图文件路径
    private $viewCreateFilePath; //创建视图文件路径
    private $viewUpdateFilePath; //修改视图文件路径
    private $saveFilePath; //操作记录文件路径

    /** 
     * 构造函数，传递所需参数
     * @param $param array
     */
    function __construct($param)
    {
        $this->randomID='Nap_'.\Yuri2::uniqueID();
        $this->data=$param['data'];
        $this->ctrlName=$param['ctrl_name'];
        $this->moduleName=$param['module_name'];
        $this->isSafe=$param['is_safe'];
        $this->txtFile=$param['txt'];
        $this->ctrlFilePath=PATH_APP.'/'.$this->moduleName.'/controller/'.$this->ctrlName.'.php';
        $viewPathPre=PATH_APP.'/'.$this->moduleName.'/view/'.$this->ctrlName.'/'.$this->model_id;
        $this->viewListFilePath=$viewPathPre.'_list.html';
        $this->viewDetailFilePath=$viewPathPre.'_detail.html';
        $this->viewCreateFilePath=$viewPathPre.'_create.html';
        $this->viewUpdateFilePath=$viewPathPre.'_update.html';
        $this->saveFilePath=PATH_APP.'/'.$this->moduleName.DS.'controller'.DS.$this->ctrlName.'.'.$this->model_id.'.create.log.html';
        $this->config_url=url($this->config_url,['ctrl_name'=>$this->ctrlName,'module_name'=>$this->moduleName,'cache_id'=>$this->randomID]);
    }

    function __get($name)
    {
        $name_trans=str_replace('_','-',$name);
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }elseif(isset($this->data[$name_trans])){
            return $this->data[$name_trans];
        }else{
            \Yuri2::throwException('未定义的ScaffoldingCURDHelper成员:'.$name);
            return false;
        }
    }

    function __set($name, $value)
    {
        $name_trans=str_replace('_','-',$name);
        if (isset($this->data[$name_trans])){
            $this->data[$name_trans]=$value;
            unset($this->$name);
        }
    }
    
    function getErrMsg(){
        return $this->errMsg;
    }

    public function checkFile(){
        $files=[
            $this->ctrlFilePath,
            $this->viewListFilePath,
            $this->viewDetailFilePath,
            $this->viewCreateFilePath,
            $this->viewUpdateFilePath,
            $this->saveFilePath,
        ];
        foreach ($files as $file){
            if (is_file($file) and $this->isSafe=='safe'){
                //非安全模式下直接覆盖不解释
                $this->errMsg="<p>文件 $file 已经存在。</p><p>为防止代码丢失，已停止文件写入。</p><p>请变更控制器名或备份并删除已存在的文件。</p>";
                return false;
            }else{
                \Yuri2::createDir(dirname($file));
            }
        }
        return true;
    }

    public function writeFile(){
        $this->writeCtrl();
        $this->writeList();
        $this->writeCreate();
        $this->writeDetail();
        $this->writeUpdate();
        $this->writeSave();
        return "<h3 class='text-success'>恭喜，代码生成完毕!</h3>
<div class='btn-group'>
    <a class='btn btn-primary' href='".url($this->moduleName.'/'.$this->ctrlName)."' target='_blank'>点击访问</a>
    <a class='btn btn-info' href='".url('SysNaples/Admin/openFile',['file_path'=>$this->saveFilePath,'is_html'=>'true'])."' target='_blank'>查看操作记录文件</a>
</div>
<div class='row' style='padding:10px'>
	<ul class='list-group'>
	<li class='list-group-item'>
		<span class='badge'>".filesize($this->ctrlFilePath)."</span>
		{$this->ctrlFilePath}
	</li>
	<li class='list-group-item'>
		<span class='badge'>".filesize($this->viewListFilePath)."</span>
		{$this->viewListFilePath}
	</li>
	<li class='list-group-item'>
		<span class='badge'>".filesize($this->viewDetailFilePath)."</span>
		{$this->viewDetailFilePath}
	</li>
	<li class='list-group-item'>
		<span class='badge'>".filesize($this->viewCreateFilePath)."</span>
		{$this->viewCreateFilePath}
	</li>
	<li class='list-group-item'>
		<span class='badge'>".filesize($this->viewUpdateFilePath)."</span>
		{$this->viewUpdateFilePath}
	</li>
	<li class='list-group-item'>
		<span class='badge'>".filesize($this->saveFilePath)."</span>
		{$this->saveFilePath}
	</li>
</ul>
</div>

";
    }

    private function writeCtrl(){
        $write_time=date('Y/m/d H:i:s');

        $doc='/**'.RN;//编写注释
        $doc.=' * 模块名:'.$this->moduleName.RN;
        $doc.=' * 控制器名:'.$this->ctrlName.RN;
        $doc.=' * 模型id:'.$this->model_id.RN;
        $doc.=' * 模型别名:'.$this->model_name.RN;
        $doc.=' * 数据库配置:'.$this->model_db.RN;
        $doc.=' * 数据库表:'.$this->model_table_name.RN;
        $doc.=' * 生成配置复制链接:'.$this->config_url.RN;
        $doc.=' */';//编写注释

        $index_for=0;
        $caps=range('A','Z') ;
        $colArr=var_export(array_keys($this->cols),true);
        $cols2exp=var_export($this->cols,true);
        $col2post=RN;
        $col2vali=RN;
        $col2names='';
        $excel_export_tbhead='';
        $excel_export_tbbody='';
        $col2row_index='';
        $pk_index=0;
        foreach ($this->cols as $k=>$v){
            $col2post.="                    '$k'=>  post('$k'),".RN;
            $col2vali.="                    \\Respect\\Validation\\Validator::stringType()->length(1,255)->check(￥￥data['$k']);".RN;
            $col2names.="'{$v['name']}',"; // [, ] added
            $excel_export_tbhead.="           ->setCellValue('{$caps[$index_for]}1', '{$v['name']}')".RN;
            $excel_export_tbbody.="           ->setCellValue('{$caps[$index_for]}'.￥￥i, ￥￥rel[￥￥i-2]['$k'])".RN;
            $col2row_index.="                        '$k'=>￥￥row[$index_for],".RN;
            if ($this->col_pk==$k){$pk_index=$index_for;}
            $index_for++;
        }

        switch ($this->model_db_type){
            case 'sqlsrv':
                $sql_page='where_raw("￥￥pk not in(select top ".(￥￥page_index-1)*￥￥page_num." ￥￥pk from ￥￥tb_name order by ￥￥order_by)")';
                break;
            default:
                $sql_page='offset((￥￥page_index-1)*￥￥page_num)';
                break;
        }
        $tpl=<<<EOT
<?php 
/** 
 * 该控制器文件由naples脚手架生成
 * 创建时间 $write_time
 */
namespace naples\app\\$this->moduleName\\controller;

use naples\\lib\\base\\Controller;
use naples\\lib\\Factory;

$doc
class $this->ctrlName extends Controller
{ //class $this->ctrlName begin

    /** 构造函数 */
    function __construct(){
        
    }
    
    /** 预配置数据库 */
    private function ModelDbInit(){
        ￥￥pk='$this->col_pk'; //声明主键
        initDb('{$this->model_db}',['id_column_overrides'=>['$this->model_table_name'=>￥￥pk]]); //初始化数据库 {$this->model_db}
    }
    
    /** 
     * 主页，默认重定向至列表页
     */
    public function index()
    {
        redirect(urlBased('{$this->model_id}_list'));
    }
    
    /**
     * 为orm对象添加条件语句
     * @param ￥￥orm \ORM
     * @param ￥￥con_col string
     * @param ￥￥con_op string
     * @param ￥￥con_val string
     * @return \ORM
     */
    private function addWhere(￥￥orm,￥￥con_col,￥￥con_op,￥￥con_val){
        if (￥￥con_op!='null'){
            switch (￥￥con_op){
                case '~=':
                    ￥￥orm=￥￥orm->whereLike(￥￥con_col,'%'.￥￥con_val.'%');
                    break;
                case '=':
                    ￥￥orm=￥￥orm->whereEqual(￥￥con_col,￥￥con_val);
                    break;
                case '>':
                    ￥￥orm=￥￥orm->where_gt(￥￥con_col,￥￥con_val);
                    break;
                case '<':
                    ￥￥orm=￥￥orm->where_lt(￥￥con_col,￥￥con_val);
                    break;
                case '>=':
                    ￥￥orm=￥￥orm->where_gte(￥￥con_col,￥￥con_val);
                    break;
                case '<=':
                    ￥￥orm=￥￥orm->where_lte(￥￥con_col,￥￥con_val);
                    break;
                case '~~':
                    ￥￥con_val_arr=explode('~~',￥￥con_val,2);
                    if (isset(￥￥con_val_arr[0]) and isset(￥￥con_val_arr[1])){
                        ￥￥orm=￥￥orm
                            ->where_gte(￥￥con_col,￥￥con_val_arr[0])
                            ->where_lte(￥￥con_col,￥￥con_val_arr[1]);
                    }
                    break;
            }
        }
        return ￥￥orm;
    }
      
    /**
     * $this->model_id 模型的列表页
     */
    public function {$this->model_id}_list(){
        ￥￥pk='$this->col_pk'; //声明主键
        ￥￥this->ModelDbInit(); //初始化数据库 {$this->model_db}
        ￥￥tb_name='{$this->model_table_name}'; //表名
        ￥￥colArr=$colArr; //读取列
        ￥￥order_by=get('order_by')?get('order_by'):'$this->col_order_by $this->col_order_rule'; //排序规则
        ￥￥order_by=preg_match('/^\w+ (asc)|(desc)$/',￥￥order_by)?￥￥order_by:'$this->col_order_by $this->col_order_rule'; //排序规则反注入检查
        ￥￥page_num=get('page_num')?get('page_num'):'$this->model_page_num'; //每一页显示条目数
        ￥￥page_index=get('page')?get('page'):1; //当前页
        ￥￥page_index=intval(get('page_goto')?get('page_goto'):￥￥page_index); //当前页
        ￥￥con_val=get('con_val')?get('con_val'):''; //条件列
        ￥￥con_col=get('con_col')?get('con_col'):''; //条件值
        ￥￥con_op=get('con_op')?get('con_op'):'null'; //条件操作符
        ￥￥orm_count=\ORM::for_table(￥￥tb_name);
        ￥￥orm_count=￥￥this->addWhere(￥￥orm_count,￥￥con_col,￥￥con_op,￥￥con_val);
        ￥￥rows_count=￥￥orm_count->count();//统计条目数
        ￥￥page_max=ceil(￥￥rows_count/￥￥page_num); //计算总页数 （向上取整）
        if (￥￥page_index>￥￥page_max){￥￥page_index=￥￥page_max;} //分页超出max的调整
        if (￥￥page_index<1){￥￥page_index=1;} //分页超出1的调整
        if (￥￥rows_count==0){
            ￥￥html_page='没有找到记录';
        }else{
            //渲染分页代码
            ￥￥html_page=RN.'<ul class="pagination">'.RN;
            ￥￥url_pre=urlBased('',['page'=>￥￥page_index-1,'order_by'=>￥￥order_by,'con_col'=>￥￥con_col,'con_val'=>￥￥con_val,'con_op'=>￥￥con_op,'page_num'=>￥￥page_num]);
            ￥￥url_next=urlBased('',['page'=>￥￥page_index+1,'order_by'=>￥￥order_by,'con_col'=>￥￥con_col,'con_val'=>￥￥con_val,'con_op'=>￥￥con_op,'page_num'=>￥￥page_num]);
            ￥￥html_page.='<li><a href="'.￥￥url_pre.'">&laquo;</a></li>'.RN;
            for (￥￥i=1;￥￥i<=￥￥page_max;￥￥i++){
                if (abs(￥￥i-￥￥page_index)>7 and ￥￥i!=1 and ￥￥i!=￥￥page_max){continue;}
                ￥￥url_i=urlBased('',['page'=>￥￥i,'order_by'=>￥￥order_by,'con_col'=>￥￥con_col,'con_val'=>￥￥con_val,'con_op'=>￥￥con_op,'page_num'=>￥￥page_num]);
                ￥￥html_page.="<li class='".(￥￥i==￥￥page_index?'active':'')."'><a href='￥￥url_i'>￥￥i</a></li>".RN;
            }
            ￥￥html_page.='<li><a href="'.￥￥url_next.'">&raquo;</a></li>'.RN;
            ￥￥html_page.='</ul>'.RN;
        }
        ￥￥rows=\ORM::for_table(￥￥tb_name)
                ->select(￥￥colArr)
                ->order_by_expr(￥￥order_by)
                ->$sql_page
                ->limit(￥￥page_num);
        ￥￥rows=￥￥this->addWhere(￥￥rows,￥￥con_col,￥￥con_op,￥￥con_val);
        ￥￥rows=￥￥rows->findArray();
        
        ￥￥this->assign('rows',￥￥rows);
        ￥￥this->assign('pk',￥￥pk);
        ￥￥this->assign('html_page',￥￥html_page);
        ￥￥this->assign('rows_count',￥￥rows_count);
        ￥￥this->assign('page_index',￥￥page_index);
        ￥￥this->assign('page_max',￥￥page_max);
        ￥￥this->assign('order_by',￥￥order_by);
        ￥￥this->assign('con_col',￥￥con_col);
        ￥￥this->assign('con_val',￥￥con_val);
        ￥￥this->assign('con_op',￥￥con_op);
        ￥￥this->assign('page_num',￥￥page_num);
        ￥￥this->assign('params',['page'=>￥￥page_index,'con_col'=>￥￥con_col,'con_val'=>￥￥con_val,'order_by'=>￥￥order_by,'con_op'=>￥￥con_op,'page_num'=>￥￥page_num]);
        ￥￥this->render();
        return ;
    }
    
    /**
     * {$this->model_id} 模型的导入页
     */
    public function {$this->model_id}_import(){
        if (!￥￥this->checkToken()){error('请勿重复提交！','back');}
        ￥￥this->ModelDbInit(); //初始化数据库 {$this->model_db}
        ￥￥tb_name='{$this->model_table_name}'; //表名
        ￥￥up=Factory::getFileUpload();
        ￥￥up->set('allowtype',['xls','xlsx']);
        if(￥￥up -> upload("file")) {
            //上传成功
            ￥￥filename=￥￥up->getFileFullPath();
            ￥￥excelHelper=Factory::getPhpExcelHelper();
            ￥￥rows=￥￥excelHelper->loadFromFile(￥￥filename)->ObjToArray();
            unlink(￥￥filename);
            array_shift(￥￥rows);
            try {
                \ORM::get_db()->beginTransaction();//开启事务
                ￥￥count_update=0;
                ￥￥count_create=0;
                foreach (￥￥rows as ￥￥row){
                    ￥￥data=[
$col2row_index      ];
                    if (\ORM::for_table(￥￥tb_name)->find_one(￥￥row[$pk_index])){
                        \ORM::for_table(￥￥tb_name)->set(￥￥data)->save();
                        ￥￥count_update++;
                    }else{
                        \ORM::for_table(￥￥tb_name)->create(￥￥data)->save();
                        ￥￥count_create++;
                    }
                }
                \ORM::get_db()->commit();//提交事务
                success('导入'.count(￥￥rows)."条数据成功<h3>新建 ￥￥count_create 条，覆盖 ￥￥count_update 条。</h3>",'back');
            }catch (\Exception ￥￥e){
                \ORM::get_db()->rollBack();//回滚事务
                error('写入数据库时发生错误','back');
            }
        } else {
            echo '<pre>';
            //获取上传失败以后的错误提示
            error(￥￥up->getErrorMsg(),'back');
        }
    }
    
    /**
     * {$this->model_id} 模型的导出页
     */
    public function {$this->model_id}_export(){
        config('show_debug_btn',false);
        ￥￥this->ModelDbInit(); //初始化数据库 {$this->model_db}
        ￥￥tb_name='{$this->model_table_name}'; //表名
        ￥￥colArr=$colArr; //读取列
        ￥￥order_by=get('order_by')?get('order_by'):'$this->col_order_by $this->col_order_rule'; //排序规则
        ￥￥con_val=get('con_val')?get('con_val'):''; //条件列
        ￥￥con_col=get('con_col')?get('con_col'):''; //条件值
        ￥￥con_op=get('con_op')?get('con_op'):'null'; //条件操作符
        ￥￥orm=\ORM::for_table(￥￥tb_name)
            ->select(￥￥colArr)
            ->order_by_expr(￥￥order_by);
        ￥￥orm=￥￥this->addWhere(￥￥orm,￥￥con_col,￥￥con_op,￥￥con_val);
        ￥￥rel=￥￥orm->findArray();
        ￥￥excelHelper=Factory::getPhpExcelHelper();
        ￥￥excelObj=￥￥excelHelper->getExcelObj();
        ￥￥excelObj->getProperties()->setCreator("{{$this->model_name}} 管理员")
            ->setLastModifiedBy("{$this->model_name} 管理员")
            ->setTitle("{$this->model_name}数据模型导出")
            ->setSubject("数据模型导出于".date('Y/m/d H:i:s'))
            ->setDescription("查询条件:￥￥con_col ￥￥con_op ￥￥con_val")
            ->setKeywords("{$this->model_name}数据模型 导出 naples")
            ->setCategory("数据模型导出文件");
        ￥￥excelObj->setActiveSheetIndex(0)
$excel_export_tbhead;
        for (￥￥i=2;￥￥i<=count(￥￥rel)+1;￥￥i++){
            ￥￥excelObj->setActiveSheetIndex(0)
$excel_export_tbbody;
        }
        ￥￥excelHelper->downloadFile('$this->model_id');
    }
                
    /**
     * $this->model_id 模型的删除页
     */
    public function {$this->model_id}_delete(){
        if (\Yuri2::isAjax()){
            config('show_debug_btn',false);
            ￥￥pk=post('pk');
            ￥￥errno=0;
            ￥￥msg='success';
            ￥￥this->ModelDbInit(); //初始化数据库 {$this->model_db}
            ￥￥tb_name='{$this->model_table_name}'; //表名
            ￥￥rel=\ORM::for_table(￥￥tb_name)->select('{$this->col_pk}')->find_one(￥￥pk);
            if (￥￥rel){
                try{
                    ￥￥rel->delete();
                }catch (\Exception ￥￥e){
                    ￥￥errno='2';
                    ￥￥msg='数据库存取错误';
                }
            }else{
                ￥￥errno='1';
                ￥￥msg='找不到要删除的条目';
            }
            return json_encode(['errno'=>￥￥errno,'msg'=>￥￥msg]);
        }else{
            error('非法访问');
            return [];
        }
    }
    
    /**
     * $this->model_id 模型的批量删除页
     */
    public function {$this->model_id}_delete_many(){
        if (\Yuri2::isAjax()){
            config('show_debug_btn',false);
            ￥￥pks=post('pks');
            ￥￥errno=0;
            ￥￥msg='success';
            ￥￥this->ModelDbInit(); //初始化数据库 $this->model_db
            ￥￥tb_name='{$this->model_table_name}'; //表名
            try{
                \ORM::for_table(￥￥tb_name)->where_id_in(￥￥pks)->delete_many();
            }catch (\Exception ￥￥e){
                ￥￥errno='2';
                ￥￥msg='数据库存取错误';
            }
            return json_encode(['errno'=>￥￥errno,'msg'=>￥￥msg]);
        }else{
            error('非法访问');
            return [];
        }
    }
                
    /**
     * $this->model_id 模型的更新页
     */
    public function {$this->model_id}_update(){
        if (\Yuri2::isPost()){
            if (￥￥this->checkToken()){
                ￥￥data=[$col2post];
                //数据验证
                try{ $col2vali
                }catch (\Exception ￥￥e){
                    error('数据验证失败:'.￥￥e->getMessage(),'back');
                }
                    ￥￥pk='$this->col_pk'; //声明主键
                    ￥￥this->ModelDbInit(); //初始化数据库 {$this->model_db}
                    ￥￥tb_name='{$this->model_table_name}'; //表名
                    ￥￥rel=\ORM::for_table(￥￥tb_name)->select(￥￥pk)->find_one(￥￥data[￥￥pk]);
                    if (!￥￥rel){
                        error('找不到需要修改的条目:'.￥￥data[￥￥pk],'back');
                    }else{
                        //尝试修改条目
                        try{
                            \ORM::get_db()->beginTransaction();//开启事务
                            ￥￥save_rel=￥￥rel->set(￥￥data)->save();
                            \ORM::get_db()->commit();//提交事务
                            if (￥￥save_rel){
                                redirect(urlBased('{$this->model_id}_detail',['pk'=>￥￥data[￥￥pk]]));
                            }else{
                                error('写入数据库时未返回结果','back');
                            }
                        }catch (\Exception ￥￥e){
                            \ORM::get_db()->rollBack();//回滚事务
                            error('写入数据库时发生错误','back');
                        }
                    }

            }else{
                error('表单令牌错误，请勿重复提交！','back');
            }
        }else{
            ￥￥pk=get('pk');
            if (!is_null(￥￥pk)){
                ￥￥this->ModelDbInit(); //初始化数据库 {$this->model_db}
                ￥￥tb_name='{$this->model_table_name}'; //表名
                ￥￥rel=\ORM::for_table(￥￥tb_name)->select(explode(',','$this->col_order'))->find_one(￥￥pk);
                if (￥￥rel){
                    ￥￥rel=￥￥rel->asArray();
                    ￥￥this->assign('pk',￥￥pk);
                    ￥￥this->assign('rel',￥￥rel);
                    ￥￥this->render();
                }else{
                    error('发生错误，找不到条目：'.￥￥pk);
                }
            }else{
                error('未指定要修改的条目');
            }
        }
        return ;
    }
                
    /**
     * {$this->model_id}模型的创建页
     */
    public function {$this->model_id}_create(){
        if (\Yuri2::isPost()){
            if (￥￥this->checkToken()){
                ￥￥data=[$col2post];
                //数据验证
                try{ $col2vali 
                }catch (\Exception ￥￥e){
                    error('数据验证失败:'.￥￥e->getMessage(),'back');
                }
                    ￥￥pk='$this->col_pk'; //声明主键
                    ￥￥this->ModelDbInit(); //初始化数据库 {$this->model_db}
                    ￥￥tb_name='{$this->model_table_name}'; //表名
                    ￥￥rel=\ORM::for_table(￥￥tb_name)->select(￥￥pk)->find_one(￥￥data[￥￥pk]);
                    if (￥￥rel){
                        error('新建时发生错误：重复的主键:'.￥￥data[￥￥pk],'back');
                    }else{
                        //尝试插入条目
                        try{
                            \ORM::get_db()->beginTransaction();//开启事务
                            ￥￥save_rel=\ORM::for_table(￥￥tb_name)->create(￥￥data)->save();
                            \ORM::get_db()->commit();//提交事务
                            if (￥￥save_rel){
                                redirect(urlBased('{$this->model_id}_detail',['pk'=>￥￥data[￥￥pk]]));
                            }else{
                                error('写入数据库时发生错误','back');
                            }
                        }catch (\Exception ￥￥e) {
                            \ORM::get_db()->rollBack();//回滚事务
                        }
                    }

            }else{
                error('表单令牌错误，请勿重复提交！','back');
            }
        }else{
            ￥￥pk=get('pk');
            if (!is_null(￥￥pk)){
                ￥￥this->ModelDbInit(); //初始化数据库 {$this->model_db}
                ￥￥tb_name='{$this->model_table_name}'; //表名
                ￥￥rel=\ORM::for_table(￥￥tb_name)->select(explode(',','$this->col_order'))->find_one(￥￥pk);
                if (￥￥rel){
                    ￥￥rel=￥￥rel->asArray();
                }else{
                    error('发生错误，找不到条目：'.￥￥pk);
                }
            }else{
                ￥￥rel=[];
            }

            ￥￥this->assign('pk',￥￥pk);
            ￥￥this->assign('rel',￥￥rel);
            ￥￥this->render();
        }
        return ;
    }
                
    /**
     * {$this->model_id}模型的详情页
     */
    public function {$this->model_id}_detail(){
        ￥￥pk=request('pk');
        ￥￥cols=$cols2exp;
        ￥￥this->ModelDbInit(); //初始化数据库 {$this->model_db}
        ￥￥tb_name='{$this->model_table_name}'; //表名
        if (!is_null(￥￥pk)){
            ￥￥rel=\ORM::for_table(￥￥tb_name)->select(explode(',','$this->col_order'))->find_one(￥￥pk);
            if (￥￥rel){
                ￥￥rel=￥￥rel->asArray();
            }else{
                error('发生错误，找不到条目：'.￥￥pk);
            }
        }else{
            ￥￥rel=[];
        }

        ￥￥this->assign('rel',￥￥rel);
        ￥￥this->assign('pk',￥￥pk);
        ￥￥this->assign('cols',￥￥cols);
        ￥￥this->render();
        return ;
    }

} //class $this->ctrlName end
EOT;
        $tpl=str_replace('￥￥','$',$tpl);
        file_put_contents($this->ctrlFilePath,$tpl);
    }

    private function writeList(){
        $cols=explode(',',$this->col_order);
        $ths='';
        $tds='';
        $con_options=RN;
        foreach ($cols as $v){
            $star_empty=$v==$this->col_pk?'':'-empty';
            $tmp=<<<EOT
                    <th style="vertical-align: middle">
                        <span class="text-primary" title="$v {$this->cols[$v]['doc']}"><span class="glyphicon glyphicon-star$star_empty"></span> {$this->cols[$v]['name']}</span>
                        <div class="btn-group" style="float: right">
                            <a type="button" href="{{url # ['order_by'=>'$v asc', 'con_col'=>￥￥con_col,'con_val'=>￥￥con_val,'con_op'=>￥￥con_op,'page_num'=>￥￥page_num] based}}" target="_self" class="btn btn-{{if ￥￥order_by=='$v asc'}}primary{{else}}default{{/}} btn-xs"><span class="glyphicon glyphicon-sort-by-attributes"></span></a>
                            <a type="button" href="{{url # ['order_by'=>'$v desc','con_col'=>￥￥con_col,'con_val'=>￥￥con_val,'con_op'=>￥￥con_op,'page_num'=>￥￥page_num] based}}" target="_self" class="btn btn-{{if ￥￥order_by=='$v desc'}}primary{{else}}default{{/}} btn-xs"><span class="glyphicon glyphicon-sort-by-attributes-alt"></span></a>
                        </div>
                    </th>
EOT;
            $ths.=$tmp.RN;
            $tds.="<td title='{{:row.$v}}'>{{:row.$v}}</td>".RN;
            $con_options.="                                    <option value='$v' {{if ￥￥con_col=='$v'}}selected{{/}}> {$this->cols[$v]['name']} </option>".RN;
        }
        $tpl=<<<EOT
<!--extend SysNaples/Index/base_bootstrap-->
<!--已使用naples bootstrap模板-->

<block_title>$this->model_name-列表</block_title>

<block_head>
    <!--head-->
    <script src="__PUBLIC__/html/js/naplesHelper.js"></script>
    {{inc SysNaples/Index/jqui}}
</block_head>

<block_body>
    <!--body-->
    <!-- 以下是由naplesPHP的脚手架工具生成的代码 标识符$this->randomID -->
    <style>
        #$this->randomID .cbx_selected_row,.cbx-th-selected-all{ -ms-transform: scale(1.5); -moz-transform: scale(1.5); -webkit-transform: scale(1.5);-o-transform: scale(1.5); }
        #$this->randomID .naples-model-search-form input{max-width: 90px;line-height: 5px;margin-right: 10px}
        #$this->randomID .tr-naples{}
        #$this->randomID td{vertical-align: middle;word-wrap:break-word ;word-break:break-all}
    </style>
    <div class="container-fluid model-list" id="$this->randomID">
        <div class="panel panel-primary">
            <!-- Default panel contents -->
            <div class="panel-heading"><span class="glyphicon glyphicon-list"></span> $this->model_name-列表<span class="model-attention"></span></div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="btn-toolbar" role="toolbar">
                            <div class="btn-group">
                                <a type="button" href="{{url {$this->model_id}_create based}}" target="_self" class="btn btn-primary "><span class="glyphicon glyphicon-plus"></span> 新增</a>
                                <a type="button" disabled class="btn btn-danger btn-del-row-many"><span class="glyphicon glyphicon-minus"></span> 批量删除</a>
                            </div>
                            <div class="btn-group">
                                <button type="button" class="btn btn-info"><span class="glyphicon glyphicon-list-alt"></span> 总计：{{:rows_count}}条</button>
                                <button type="button" class="btn btn-info"><span class="glyphicon glyphicon-bookmark"></span> 当前：第{{:page_index}}/{{:page_max}}页</button>
                            </div>
                            {{if ￥￥con_op!='null'}}
                            <div class="btn-group">
                                <button type="button" class="btn btn-success"><span class="glyphicon glyphicon-search"></span> 条件：{{:con_col}} {{:con_op}} {{:con_val}}</button>
                                <a type="button" href="{{url # ['order_by'=>￥￥order_by] based}}" class="btn btn-warning"><span class="glyphicon glyphicon-zoom-out"></span> 清除条件</a>
                            </div>
                            {{/}}
                            <div class="btn-group">
                                <a class="btn btn-primary" data-toggle="modal" href="#modal-$this->randomID"><span class="glyphicon glyphicon-floppy-open"></span> 导入</a>
                                <a type="button"  onclick="if (!confirm('确认导出当前页数据？')){return false;}" href="{{url {$this->model_id}_export ['order_by'=>￥￥order_by, 'con_col'=>￥￥con_col,'con_val'=>￥￥con_val,'con_op'=>￥￥con_op,'page_num'=>￥￥page_num,'import'=>'out'] based}}" target="_self" class="btn btn-primary "><span class="glyphicon glyphicon-floppy-save"></span> 导出</a>
                            </div>
                            <div class="btn-group">
                                <a class="btn btn-success" data-toggle="modal" href="#modal-tools-{$this->randomID}"><span class="glyphicon glyphicon-wrench"></span> 工具箱</a>
                            </div>
                            <form action="" method="get" class="form-inline" style="float: right" role="form">
                                {{hidden ￥￥params}}
                                <div class="form-group">
                                    <label class="sr-only" ></label>
                                    <input type="text" spellcheck="false" class="form-control" name="page_goto" style='max-width:80px'  placeholder="直达某页">
                                </div>
                                <select name="page_num" onchange="￥￥(this).parents('form.form-inline').submit()"   class="form-control">
                                    <option value="5" {{if ￥￥page_num=='5'}}selected{{/}}>每页5条记录</option>
                                    <option value="10" {{if ￥￥page_num=='10'}}selected{{/}}>每页10条记录</option>
                                    <option value="20" {{if ￥￥page_num=='20'}}selected{{/}}>每页20条记录</option>
                                    <option value="50" {{if ￥￥page_num=='50'}}selected{{/}}>每页50条记录</option>
                                    <option value="100" {{if ￥￥page_num=='100'}}selected{{/}}>每页100条记录</option>
                                    <option value="200" {{if ￥￥page_num=='200'}}selected{{/}}>每页200条记录</option>
                                    <option value="500" {{if ￥￥page_num=='500'}}selected{{/}}>每页500条记录</option>
                                </select>
                                <select name="con_col"  class="form-control">
                                    $con_options
                                </select>
                                <select name="con_op"  class="form-control">
                                	<option value="null" {{if ￥￥con_op=='null'}}selected{{/}}>  无条件  </option>
                                	<option value="~=" {{if ￥￥con_op=='~='}}selected{{/}}>  约等于  </option>
                                	<option value="=" {{if ￥￥con_op=='='}}selected{{/}}>  等于  </option>
                                	<option value=">" {{if ￥￥con_op=='>'}}selected{{/}}>  大于  </option>
                                	<option value="<" {{if ￥￥con_op=='<'}}selected{{/}}>  小于  </option>
                                	<option value=">=" {{if ￥￥con_op=='>='}}selected{{/}}>  大于或等于  </option>
                                	<option value="<=" {{if ￥￥con_op=='<='}}selected{{/}}>  小于或等于  </option>
                                	<option value="~~" {{if ￥￥con_op=='~~'}}selected{{/}}>  区间 a~~b  </option>
                                </select>
                                <div class="form-group">
                                    <label class="sr-only" ></label>
                                    <input type="text" spellcheck="false" class="form-control" name="con_val" value="{{:con_val}}"  placeholder="查询值">
                                </div>
                            	<button type="submit" class="btn btn-primary">查询</button>
                            </form>
                        </div>
                        <div class="slider hidden"  style="margin-top: 15px;background-color: #efefef;"></div>
                    </div>
                </div>
            </div>
            <!-- Table -->
            <table class="table table-hover table-bordered table-striped" style="border-bottom: 1px solid rgb(221,221,221)">
                <thead>
                <tr>
                    <th style="width: 30px" class="cbx"><input class="cbx-th-selected-all" type='checkbox'></th>
                    <th style="width: 220px;vertical-align: middle">
                        <span class="text-warning" title="对当前行所指条目进行操作"><span class="glyphicon glyphicon-paperclip"></span> 操作</span>
                    </th>
                    $ths
                </tr>
                </thead>
                <tbody>
                {{each ￥￥rows ￥￥row}}
                <tr class="tr-naples hidden" pk="{{?echo ￥￥row[￥￥pk]}}">
                    <td class="cbx"><input type='checkbox' class="cbx_selected_row"></td>
                    <td>
                        <div class="btn-toolbar" role="toolbar">
                            <div class="btn-group">
                                <a type="button" href="{{url {$this->model_id}_detail ['pk'=>￥￥row[￥￥pk]] based}}" target="_self" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-info-sign"></span> 详情</a>
                                <a type="button" href="{{url {$this->model_id}_create ['pk'=>￥￥row[￥￥pk]] based}}" target="_self" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-file"></span> 拷贝</a>
                                <a type="button" href="{{url {$this->model_id}_update ['pk'=>￥￥row[￥￥pk]] based}}" target="_self" class="btn btn-warning btn-xs"><span class="glyphicon glyphicon-edit"></span> 修改</a>
                                <a type="button"  class="btn btn-danger btn-xs btn-del-row"><span class="glyphicon glyphicon-minus"></span> 删除</a>
                            </div>
                        </div></td>
                    $tds
                </tr>
                {{/}}
                </tbody>
            </table>
            <div class="row model-footer">
                <div class="col-md-6" style="margin-left: 10px">
                    {{:html_page |no}}
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-$this->randomID">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">从excel文件导入数据</h4>
                    </div>
                    <div class="modal-body">
                        <div class="modal-body">
                        <form action="{{url {$this->model_id}_import based}}" method="post" class="form-horizontal frm-up" enctype="multipart/form-data" role="form">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">上传文件</label>
                                <div class="col-sm-10">
                                    <input type="file" required class="form-control ipt-up" name="file" placeholder="上传表格文件">
                                </div>
                            </div>
                            {{token frm_up}}
                        </form>
                    </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary btn-up-frm">确定</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <div class="modal fade" id="modal-tools-$this->randomID">
        	<div class="modal-dialog">
        		<div class="modal-content">
        			<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        				<h4 class="modal-title">常用辅助工具</h4>
        			</div>
        			<div class="modal-body">
        				<form action="#" onsubmit="return false;" class="form-horizontal"  role="form">
                            <div class="form-group">
                                <label for="dtp-rel-{$this->randomID}" class="col-sm-2 control-label">时间日期</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="dtp-rel-{$this->randomID}" onchange="$('#dtp-timestamp-{$this->randomID}').val(Date.parse(new Date($(this).val()))/1000);" placeholder="点击选择">
                                </div>
                            </div>
                            <div class="form-group">
                                <label  class="col-sm-2 control-label">时间戳(s)</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="dtp-timestamp-{$this->randomID}" onkeyup="var newDate_{$this->randomID} = new Date(); newDate_{$this->randomID}.setTime($('#dtp-timestamp-{$this->randomID}').val() * 1000);$('#dtp-rel-{$this->randomID}').val(newDate_{$this->randomID}.Format('yyyy-MM-dd hh:mm:ss')); " placeholder="数字型时间戳">
                                </div>
                            </div>
                        </form>
        			</div>
        			<div class="modal-footer">
        				<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
        			</div>
        		</div><!-- /.modal-content -->
        	</div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div>
    <script type="text/javascript">
        ￥￥(function () {
        
            //导入
            $("#$this->randomID .btn-up-frm").click(function () {
                if($('#$this->randomID .ipt-up').val()){
                    $('#$this->randomID .frm-up').submit();                
                }
            });
        
            //滑块
            var trs_$this->randomID=$("#$this->randomID .tr-naples");
            var tr_len_$this->randomID=trs_$this->randomID.length;
            if (tr_len_$this->randomID>10){
                $("#$this->randomID .slider").removeClass('hidden')
            }
            $("#$this->randomID .slider").slider({
                min:1,
                max:tr_len_$this->randomID,
                animate: true,
                values: 1 ,
                slide:function(event, ui) {
                    var val=ui.value-1;
                    trs_$this->randomID.each(function (index,node) {
                        node=$(node);
                        var min=val-4>tr_len_$this->randomID-10?tr_len_$this->randomID-10:val-4;
                        var max=val+5<9?9:val+5;
                        if ((index>=min && index<=max)){
                            node.removeClass('hidden');
                        }else{
                            node.addClass('hidden');
                        }
                    })
                }
            });
            trs_$this->randomID.each(function (index,node) {
                node=$(node);
                var min=0;
                var max=9;
                if ((index>=min && index<=max)){
                    node.removeClass('hidden');
                }else{
                    node.addClass('hidden');
                }
            });
            
            //滚动滑块
            $('#$this->randomID tbody').mousewheel(function(event) {
                var slider=$("#$this->randomID .slider");
                var value=slider.slider( "value");
                value=parseInt(value-event.deltaY*(tr_len_$this->randomID/33+event.deltaFactor/100));
                if(value>tr_len_$this->randomID){value=tr_len_$this->randomID;}
                if(value<1){value=1;}
                slider.slider( "value",value);
                trs_$this->randomID.each(function (index,node) {
                    node=$(node);
                    var min=value-4>tr_len_$this->randomID-10?tr_len_$this->randomID-10:value-4;
                    var max=value+5<9?9:value+5;
                    if ((index>=min && index<=max)){
                        node.removeClass('hidden');
                    }else{
                        node.addClass('hidden');
                    }
                });
                return false;
            });
            
            //批量删除
            $("#$this->randomID .btn-del-row-many").click(function () {
                var cbxs=$("#$this->randomID .cbx_selected_row");
                var count_checked=0;
                var pks=[];
                cbxs.each(function () {
                    if ($(this).prop("checked")){
                        count_checked++;
                        var pk=$(this).parents('tr').attr('pk');
                        pks.push(pk);
                    }
                });
                if (confirm('确定要批量删除选中的'+count_checked+'个条目吗？')){
                    var attention=$("#$this->randomID .model-attention");
                    attention.html('[正在删除，请稍候...]');
                    $.ajax({
                        type: "POST",
                        url: "{{url {$this->model_id}_delete_many based}}",
                        data: {
                            pks:pks
                        },
                        dataType: "json",
                        async:false,

                        success: function(data){
                            if (data.errno!=0){
                                alert('很抱歉，操作发生错误。错误码:'+data.errno+'错误信息:'+data.msg)
                            }
                            location.reload();
                        },
                        error:function () {
                            alert('错误，远程服务器没有响应.')
                        }
                    });
                }
                return false;
            });

            //删除
            ￥￥("#$this->randomID .btn-del-row").click(function () {
                if (!confirm('确定要删除此条目吗？')){
                    return false;
                }else{
                    var pk=￥￥(this).parents('tr').attr('pk');
                    var attention=￥￥("#$this->randomID .model-attention");
                    attention.html('[正在删除，请稍候...]');
                    ￥￥.ajax({
                        type: "POST",
                        url: "{{url {$this->model_id}_delete based}}",
                        data: {
                            pk:pk
                        },
                        dataType: "json",
                        async:false,

                        success: function(data){
                            if (data.errno!=0){
                                alert('很抱歉，操作发生错误。错误码:'+data.errno+'错误信息:'+data.msg)
                            }
                            location.reload();
                        },
                        error:function () {
                            alert('错误，远程服务器没有响应.')
                        }
                    });
                    return false;
                }
            });
            
            //选择框相关
            $("#$this->randomID .cbx_selected_row").change(function () {
                //检查全选状态
                var cbxs=$("#$this->randomID .cbx_selected_row");
                var count_checked=0;
                var count_num=0;
                cbxs.each(function () {
                    if ($(this).prop("checked")){
                        count_checked++;
                    }
                    count_num++;
                });
                var cbx_all= $("#$this->randomID .cbx-th-selected-all");
                if (count_checked>0 &&  count_checked==count_num){
                    cbx_all.prop("checked", true);
                }else{
                    cbx_all.prop("checked", false);
                }
                if (count_checked>0){
                    $("#$this->randomID .btn-del-row-many").removeAttr('disabled');
                }else{
                    $("#$this->randomID .btn-del-row-many").attr('disabled',true);
                }
            });
            
            //选择框相关
            $("#$this->randomID .cbx-th-selected-all").change(function () {
                var cbx=$(this);
                var cbxs=$("#$this->randomID .cbx_selected_row");
                if (!cbx.prop("checked")) {
                    cbxs.each(function () {
                        $(this).prop("checked", false);
                        $("#$this->randomID .btn-del-row-many").attr('disabled',true);
                    })
                } else {
                    cbxs.each(function () {
                        $(this).prop("checked", true);
                        if (cbxs.length>0){
                            $("#$this->randomID .btn-del-row-many").removeAttr('disabled');
                        }
                    })
                }
            });
            
            //小工具
            $('#dtp-rel-{$this->randomID}').datetimepicker({
                language:  'zh-CN',
                weekStart: 1,
                todayBtn:  1,
		        autoclose: 1,
		        todayHighlight: 1,
		        startView: 2,
		        forceParse: 0,
                showMeridian: 1,
                format: 'yyyy-mm-dd hh:ii:ss',
            });
        })
    </script>
    <!-- 以上是由naplesPHP的脚手架工具生成的代码 标识符$this->randomID -->
</block_body>
EOT;
        $tpl=str_replace('￥￥','$',$tpl);
        file_put_contents($this->viewListFilePath,$tpl);
    }

    private function writeCreate(){
        $cols=explode(',',$this->col_order);
        $grps='';
        foreach ($cols as $v){
            $tmp=<<<EOT
                        <div class="form-group">
                            <label for="ipt-$v" title="$v" class="col-sm-2 control-label">{$this->cols[$v]['name']}</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="ipt-$v" name="$v" value="{{:rel.$v}}" placeholder="{$this->cols[$v]['doc']}">
                            </div>
                        </div>
EOT;
            $grps.=$tmp.RN;
        }
        $tpl=<<<EOT
<!--extend SysNaples/Index/base_bootstrap-->
<!--已使用naples bootstrap模板-->

<block_title>$this->model_name-新建</block_title>

<block_head>
    <!--head-->
</block_head>

<block_body>
    <!--body-->
    <!-- 以下是由naplesPHP的脚手架工具生成的代码 标识符$this->randomID -->
    <div class="container-fluid model-list" id="$this->randomID">
        <div class="panel panel-primary">
            <div class="panel-heading"><span class="glyphicon glyphicon-plus"></span> $this->model_name-新建</div>
            <div class="panel-body" style="padding: 30px">
                <form action="" method="post" class="form-horizontal" role="form">
                    <div class="form-group">
                        <div class="btn-toolbar col-sm-10 col-sm-push-2" role="toolbar">
                            <div class="btn-group">
                                <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-save"></span> 提交</button>
                            </div>
                            <div class="btn-group">
                                <a type="button" class="btn btn-info" href="{{url {$this->model_id}_list based}}"><span class="glyphicon glyphicon-list"></span> 返回列表</a>
                                <a type="button" class="btn btn-info" href="javascript:history.go(-1)"><span class="glyphicon glyphicon-circle-arrow-left"></span> 返回上一页</a>
                            </div>
                            {{if !is_null(￥￥pk)}}
                            <div class="btn-group">
                                <a type="button" title="点击查看数据复制源详情" class="btn btn-default" href="{{url {$this->model_id}_detail ['pk'=>￥￥pk] based}}" target="_blank"><span class="glyphicon glyphicon-info-sign"></span> 初始数据基于其他条目</a>
                            </div>
                            {{/}}
                        </div>
                    </div>
                    <div class="form-group">
                        $grps
                        {{token}}
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- 以上是由naplesPHP的脚手架工具生成的代码 标识符$this->randomID -->
</block_body>
EOT;
        $tpl=str_replace('￥￥','$',$tpl);
        file_put_contents($this->viewCreateFilePath,$tpl);
    }

    private function writeDetail(){
        $cols=explode(',',$this->col_order);
        $detail_trs='';
        foreach ($cols as $col){
            $detail_trs.=<<<EOT
                <tr>
                    <th title="$col {$this->cols[$col]['doc']}" >{$this->cols[$col]['name']}</th>
                    <td>{{:rel.$col}}</td>
                </tr>

EOT;
        }
        $tpl=<<<EOT
<!--extend SysNaples/Index/base_bootstrap-->
<!--已使用naples bootstrap模板-->

<block_title>$this->model_name-详情</block_title>

<block_head>
    <!--head-->
</block_head>

<block_body>
    <!--body-->
    <!-- 以下是由naplesPHP的脚手架工具生成的代码 标识符$this->randomID -->
    <div class="container-fluid model-list" id="$this->randomID">
        <div class="panel panel-primary">
            <div class="panel-heading"><span class="glyphicon glyphicon-info-sign"></span> {$this->model_name}-{{:pk}} <span class="model-attention"></span></div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="btn-toolbar" role="toolbar">
                            <div class="btn-group">
                                <a type="button" class="btn btn-info" href="{{url {$this->model_id}_list based}}"><span class="glyphicon glyphicon-list"></span> 返回列表</a>
                                <a type="button" class="btn btn-info" href="javascript:history.go(-1)"><span class="glyphicon glyphicon-circle-arrow-left"></span> 返回上一页</a>
                            </div>
                            <div class="btn-group">
                                <a type="button" class="btn btn-success " href="{{url {$this->model_id}_create based}}"><span class="glyphicon glyphicon-plus"></span> 新建</a>
                                <a type="button" class="btn btn-success " href="{{url {$this->model_id}_create ['pk'=>￥￥pk] based}}"><span class="glyphicon glyphicon-file"></span> 拷贝</a>
                            </div>
                            <div class="btn-group">
                                <a type="button" class="btn btn-warning " href="{{url {$this->model_id}_update ['pk'=>￥￥pk] based}}"><span class="glyphicon glyphicon-edit"></span> 修改</a>
                            </div>
                            <div class="btn-group">
                                <a type="button" class="btn btn-danger  btn-del-row" href="#"><span class="glyphicon glyphicon-trash"></span> 删除</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <table class="table table-hover table-bordered table-striped">
                <tbody>
                $detail_trs
                </tbody>
            </table>
        </div>
        <script type="text/javascript">
            ￥￥(function () {
                ￥￥("#$this->randomID .btn-del-row").click(function () {
                    if (!confirm('确定要删除此条目吗？')){
                        return false;
                    }else{
                        var pk='{{:pk}}';
                        var attention=￥￥("#$this->randomID .model-attention");
                        attention.html('[正在删除，请稍候...]');
                        ￥￥.ajax({
                            type: "POST",
                            url: "{{url {$this->model_id}_delete based}}",
                            data: {
                                pk:pk
                            },
                            dataType: "json",
                            async:false,

                            success: function(data){
                                if (data.errno!=0){
                                    alert('很抱歉，操作发生错误。错误码:'+data.errno+'错误信息:'+data.msg)
                                }
                                location='{{url {$this->model_id}_list based}}';
                            },
                            error:function () {
                                alert('错误，远程服务器没有响应.')
                            }
                        });
                        return false;
                    }
                })
            })
        </script>
    </div>
    <!-- 以上是由naplesPHP的脚手架工具生成的代码 标识符$this->randomID -->
</block_body>
EOT;
        $tpl=str_replace('￥￥','$',$tpl);
        file_put_contents($this->viewDetailFilePath,$tpl);
    }

    private function writeUpdate(){
        $cols=explode(',',$this->col_order);
        $grps='';
        foreach ($cols as $v){
            $is_readonly=$v==$this->col_pk?' readonly ':'';
            $tmp=<<<EOT
                        <div class="form-group">
                            <label for="ipt-$v" title="$v" class="col-sm-2 control-label">{$this->cols[$v]['name']}</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="ipt-$v" name="$v" value="{{:rel.$v}}" $is_readonly placeholder="{$this->cols[$v]['doc']}">
                            </div>
                        </div>
EOT;
            $grps.=$tmp.RN;
        }
        $tpl=<<<EOT
<!--extend SysNaples/Index/base_bootstrap-->
<!--已使用naples bootstrap模板-->

<block_title>$this->model_name-修改-{{:pk}}</block_title>

<block_head>
    <!--head-->
</block_head>

<block_body>
    <!--body-->
    <!-- 以下是由naplesPHP的脚手架工具生成的代码 标识符$this->randomID -->
    <div class="container-fluid model-list" id="$this->randomID">
        <div class="panel panel-primary">
            <div class="panel-heading"><span class="glyphicon glyphicon-edit"></span> $this->model_name-修改-{{:pk}}</div>
            <div class="panel-body" style="padding: 30px">
                <form action="" method="post" class="form-horizontal" role="form">
                    <div class="form-group">
                        <div class="btn-toolbar col-sm-10 col-sm-push-2" role="toolbar">
                            <div class="btn-group">
                                <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-save"></span> 提交</button>
                            </div>
                            <div class="btn-group">
                                <a type="button" class="btn btn-info" href="{{url {$this->model_id}_list based}}"><span class="glyphicon glyphicon-list"></span> 返回列表</a>
                                <a type="button" class="btn btn-info" href="javascript:history.go(-1)"><span class="glyphicon glyphicon-circle-arrow-left"></span> 返回上一页</a>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        $grps
                        {{token}}
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- 以上是由naplesPHP的脚手架工具生成的代码 标识符$this->randomID -->
</block_body>
EOT;
        $tpl=str_replace('￥￥','$',$tpl);
        file_put_contents($this->viewUpdateFilePath,$tpl);
    }

    private function writeSave(){
        $this->txtFile=htmlspecialchars($this->txtFile);
        cache($this->randomID,$this->txtFile,3600);//保存文字到缓存
        $write_time=date('Y/m/d H:i:s');
        $data2str=dump($this->data,false);
        $txt=<<<EOT
<meta charset="utf-8">
<p>这是由naples_php CURD代码脚手架生成的html文件</p>
<p>如果您确定用不到这个文件了，请将它删除，以免造成安全隐患</p>
<hr>
<p>生成时间:$write_time</p>
<p>模块名:$this->moduleName</p>
<p>控制器名:$this->ctrlName</p>
<p>模型id:$this->model_id</p>
<p>模型别名:$this->model_name</p>
<p>数据库配置:$this->model_db</p>
<p>数据库表:$this->model_table_name</p>
<a href='$this->config_url' target='_blank' title='可以通过这个链接还原当时的配置'>重新生成</a>
<hr>
<p>其他信息</p>
$data2str
<hr>
<textarea spellcheck="false" style="width: 100%;height:600px;border:none">
{$this->txtFile}
</textarea>
EOT;
        file_put_contents($this->saveFilePath,$txt);

    }

}