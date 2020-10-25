<?php
namespace Admin\Controller;
use Db\Api\DbApi;
use Think\Upload;

class ConfigController extends AdminController {
    
    /**
     * 表管理
     */
    public function index(){
    	$this->meta_title = '系统设置';

    	/* 查询条件初始化 */
    	$list = $this->lists('Db','s_', 'tid');

		// 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);

		$this->assign('list', $list);

        $this->display();
    }

    /**
     * 新增表
     */
    public function add(){
        if(IS_POST){
            $Db = new DbApi();
            $tname = I('post.tname');
            $tnamec= I('post.tnamec');
            $cellArray=array();
            //获取表字段
            for ($i=0; $i < I('post.dbcellindex'); $i++) { 
                $cell=array(
                    'tname'=>I('tname'.($i+1)),
                    'cname'=>I('cname'.($i+1)),
                    'tcd'=>I('tcd'.($i+1)),
                    'ttype'=>I('ttype'.($i+1)),
                    );
                $cellArray[]=$cell;
            }
            if($cellArray){
                if($Db->CreateApi($tname,$tnamec,$cellArray)){
                    S('DB_CONFIG_DATA',null);
                    $this->success('新增成功', U('index'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Config->getError());
            }
        } else {
            $this->meta_title = '新增配置';
			
            $Db = new DbApi();

			//共享表
            $this->assign('sharetable',$Db->Get_Share());

			//字典表
            $this->assign('dicttable',$Db->Get_Dict());


            $this->display('add');
        }
    }


    public function edit(){
        $tid=I('id');
        if(IS_POST){
            $Db = new DbApi();
            $tname = I('post.tname');
            $tnamec= I('post.tnamec');
            $cellArray=array();
            //获取表字段
            for ($i=0; $i < I('post.dbcellindex'); $i++) { 
                $cell=array(
                    'tname'=>I('tname'.($i+1)),
                    'cname'=>I('cname'.($i+1)),
                    'tcd'=>I('tcd'.($i+1)),
                    'ttype'=>I('ttype'.($i+1)),
                    );
                $cellArray[]=$cell;
            }
            if($cellArray){
                if($Db->UpdateApi($tid,$tname,$tnamec,$cellArray)){
                    S('DB_CONFIG_DATA',null);
                    $this->success('新增成功', U('index'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Config->getError());
            }
            
        }else{
            $this->meta_title = '编辑配置';

            $Db = new DbApi();

            $this->assign('info',$Db->GetDBModelForTid($tid));

            $cell = $Db->GetColumnFortid($tid);
            $this->assign("cell",$cell);
            $this->assign("cellcount",count($cell));

            $this->display();
        }
    }


    /**
     * 显示表字段
     */
	public function tableShow($id){
        $Db = new DbApi();

		$viewList = $Db->GetColumnFortid($id);

        $this->assign('viewList', $viewList);

		$this->display();
	}

    /**
     * 视图管理
     */
    public function tableView($id){

        $this->meta_title = '视图管理';

        $Db = new DbApi();

        $viewList = $Db->GetTableView($id);

        $array['tid'] = $id;
        $array['viewList'] = $viewList;

        $this->assign($array);

        $this->display();
    }

    /**
     * 添加编辑视图
     * 获取tid
     */
	public function editView(){

		$tid=I('tid');
		$vid=I('vid');
        $Db = new DbApi();
		
        if(IS_POST){
			
			$editid=I('editid');
			
			$viewValue=array();

			$viewValue['vid']=$vid;
			$viewValue['tid']=$tid;
			$viewValue['tname']=I('tname');//表名
			$viewValue['vname']=I('vname');//视图名
			$viewValue['vtype']=I('vtype');//类型


			$viewList = $Db->GetColumnForVid($viewValue['vid']);

			$list=array();
			foreach ($viewList as $key => $value) {
				if(I('use_'.$value['fname'])=='on'){
					$listArray=array();
					$listArray['tname']=$value['fname'];
					$listArray['cname']=I('cname_'.$value['fname']);
					$listArray['use']=I('use_'.$value['fname']);
					$listArray['create']=I('create_'.$value['fname']);
					$listArray['readonly']=I('readonly_'.$value['fname']);
					$listArray['order']=I('order_'.$value['fname']);
					$listArray['dict']=I('dict_'.$value['fname']);
					$listArray['share']=I('share_'.$value['fname']);
					$list[]=$listArray;
				}
			}
			$viewValue['list']=$list;
			if($editid){
				$result = $Db->UpdateViewForm($viewValue,$vid);
			}else{
				$result = $Db->CreateViewForm($viewValue);
			}
            if($result){
					S('DB_CONFIG_DATA',null);
					$this->success('新增成功', U('tableView?id='.$tid));

			} else {
				$this->error('新增失败');
			}

		}else{

            $this->meta_title = '视图操作';
			
 			$view = $Db->GetTableView($tid);

			if(!$vid){
				$vid=$view[0]['vid'];
			}else{
				$array['editid']=1;
				$viewForm = $Db->GetTableViewVid($vid);
			}

			$viewList = $Db->GetColumnForVid($vid);

			$array['tid'] = $tid;
			$array['vid'] = $vid;
			$array['tname'] = $view[0]['form_table'];
			$array['viewList'] = $viewList;
			$array['viewForm'] = $viewForm;

			//共享表
			$array['sharetable'] = $Db->Get_Share();

			//字典表
			$array['dicttable'] = $Db->Get_Dict();

			$this->assign($array);
			$this->display();
		}
	}

    /**
     * 删除视图
     */
	public function delView(){
		
		$vid=I('get.vid');
		$tid=I('get.tid');

        $Db = new DbApi();
		if($Db->DelView($vid)){
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}
	}

    /**
     * 视图数据列表
     */
    public function viewList(){
        $vId=I('get.vid');
		$tId=I('get.tid');
        $st = I('get.name');//查询条件
        
        $this->meta_title = '视图数据';

        $Db = new DbApi();

        $view_column=$Db->GetColumnForVid($vId);//获取列头

        $where = extra_where($view_column,$st);

        $view_List = $Db->GetListForVid($vId,C('PAGE_SIZE'),$where);//数据和分页

        $array['viewList'] = $view_List['list'];

        $array['viewcolumn'] = $view_column;

        $array['_page']=$view_List['page'];//分页

        $array['vid'] = $vId;

        $array['tid'] = $tId;

        $this->assign($array);

        $this->display();
    }


    /**
     * 数据编辑、新增
     */
    public function editDB(){

        $Db = new DbApi();

        if(IS_POST){
            $vid=I("post._vid");
            $mid=I("post._mid");

            //获取v_dbcell内容
            $column_list=$Db->GetColumnForVid($vid);

            $value_list=array();

            //获取前台提交表单，并且合并对应的字段
            foreach ($column_list as $key => $value) {

                $value['id']=0;

                if ($value['fname']=='update_time'||$value['fname']=='create_time') {

                    $value['name']=$value['fname'];
                    $value['value']= date('Y-m-d H:i:s',time());

                }else{

                    $post_value=I("post.".$value['fname']);
                    $value['name']=$value['fname'];

                    if ($post_value) {

						if($value['flx']=='password'){
						}
                        $value['value']= $post_value;

                    }else{

                        $value['value']= "";
                        $value['id']=-1;

                    }
                }

                $value_list[]=$value;
            }

			if($mid){
				$result =$Db->Update_Form_Data($value_list,$mid);
			}else{
				$result =$Db->Insert_Form_Data($value_list);
			}
            if($result){
                S('DB_CONFIG_DATA',null);
                $this->success('操作成功', U('viewList',array('vid'=>$vid,'tid'=>$column_list[0]['tid'])));
            } else {
                $this->error('操作失败');
            }
        }else{
            $vId=I('get.vid');
            $mid=I('get.id');

            $this->meta_title = ($mid) ? '编辑数据' : '新增数据' ;

            $this->assign('allhtml',$Db->CreateHtml($vId,$mid));
            $this->display();
        }

    }

    /**
     * 删除数据
     */
	public function delDB(){
		$vid=I("get.vid");

		$mid = I('get.id');

        if ( empty($mid) ) {
            $this->error('请选择要操作的数据!');
        }

        $Db = new DbApi();

		$result =$Db->Del_Form_Data($vid,$mid);

		if($result){
			S('DB_CONFIG_DATA',null);
			$this->success('操作成功', U('viewList',array('vid'=>$vid)));
		} else {
			$this->error('操作失败');
		}

	}
}