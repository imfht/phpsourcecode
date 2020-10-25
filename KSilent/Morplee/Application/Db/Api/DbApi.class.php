<?php

namespace Db\Api;
use Db\Api\Api;
use Db\Model\DbModel;
use Db\Model\DbformModel;
use Db\Model\TableModel;
use Db\Model\DbcellModel;
use Db\Model\VDbcellModel;
use Db\Model\DictModel;

class DbApi extends Api{

    /**
     * 构造方法，实例化操作模型
     */
    protected function _init(){
    }

    /**
     * 创建新表
     * @param  string $tname 表名
     * @param  string $tnamec 别称
     * @return integer        注册结果
     */
    public function CreateApi($tname,$tnamec,$cellArray){

        //创建s_db表数据
        $dbmodel =new DbModel();

        $dbmodel->startTrans();

        $dbid=$dbmodel->InsertDb($tname,$tnamec);//创建表信息

        if ($dbid) {
            //创建视图s_dbform
            $dbForm=array($dbid,'list',$tnamec.'-默认视图',$tname);

            $dbform=new DbformModel();

            $formId = $dbform->InsertDbForm($dbForm);//创建视图信息

            if ($formId) {

                $dbcell=new DbcellModel();
                
                $dbcellret = $dbcell->InsertDbcell($dbid,$cellArray);//创建字段信息

                if ($dbcellret) {
                    $vcell=new VDbcellModel();
                    $result = $vcell->InsertVDbcell($formId,$dbid,$cellArray);//创建关联字段信息

                    if ($result) {

                        $dbtable=new TableModel();
                        $create_result = $dbtable->CreateTable($tname,$cellArray);//创建物理表

                        if ($create_result) {//都确定完成，提交所有数据操作
                            $dbmodel->commit(); 
                            return true;
                        }else{
                            $dbmodel->rollback(); 
                            return false;
                        }
                    } else {
                        $dbmodel->rollback(); 
                        return false;
                    }
                } else {
                    $dbmodel->rollback(); 
                    return false;
                }
                
            }
        }else{
            $dbmodel->rollback(); 
            return false;
        }
    }

    /**
     * 删除表
     * @param  int $tid 表编号
     * @param  int $tname 表名
     * @return integer  删除结果
     */
    public function UpdateApi($tid,$tname,$tnamec,$cellArray){
        //创建s_db表数据
        $dbmodel = new DbModel();
        $dbmodel->startTrans();

        if ($tid) {
            $dbmodel->UpDBTnamec($tid,$tnamec);//更新表信息

            //创建视图s_dbform
            $dbForm = array($tnamec.'-默认视图');
            $dbform = new DbformModel();
            $dbform_list = $dbform->GetDBFormList($tid);
            foreach ($dbform_list as $key => $value) {
                if (strstr($value["vname"],"-默认视图")) {
                    $dbform->UpdateDbFormForTid($dbForm,$tid);//修改视图信息
                    $formId = $value["vid"];
                }
            }
            if ($formId) {
                $dbcell = new DbcellModel();
                $db_num = $dbcell->DeleteDbCell($tid);

                $vcell = new VDbcellModel();
                $vc_num = $vcell->DeleteVDbCell($tid);

                $dbtable = new TableModel();
                $dbt_num = $dbtable->DelTable($tname);

                $dbcell=new DbcellModel();
                
                $dbcellret = $dbcell->InsertDbcell($tid,$cellArray);//创建字段信息
                if ($dbcellret) {
                    $vcell=new VDbcellModel();
                    $result = $vcell->InsertVDbcell($formId,$tid,$cellArray);//创建关联字段信息

                    if ($result) {

                        $dbtable=new TableModel();
                        $create_result = $dbtable->CreateTable($tname,$cellArray);//创建物理表

                        if ($create_result) {//都确定完成，提交所有数据操作
                            $dbmodel->commit(); 
                            return true;
                        }else{
                            $dbmodel->rollback(); 
                            return false;
                        }
                    } else {
                        $dbmodel->rollback(); 
                        return false;
                    }
                }else{
                    $dbmodel->rollback(); 
                    return false;
                }
            }else{
                $dbmodel->rollback(); 
                return false;
            }
        }else{
            $dbmodel->rollback(); 
            return false;
        }
    }

    /**
     * 创建视图
     */
	public function CreateViewForm($valueList,$vid){
		
		$dbform=new DbformModel();

        $dbform->startTrans();

		//创建视图s_dbform
        $dbForm=array($valueList['tid'],$valueList['vtype'],$valueList['vname'],$valueList['tname']);

		$formId = $dbform->InsertDbForm($dbForm);//创建视图信息
		
		if($formId){
			$dbcell=new VDbcellModel();
			$cellArray=array();

			foreach ($valueList['list'] as $key => $value) {
				$cell=array(
					'tname'=>$value['tname'],
					'cname'=>$value['cname'],
					'vtype'=>$valueList['vtype'],
					'indexorder'=>$value['order'],
					'ispk'=>$value['use'],
					'isnotnull'=>$value['create'],
					'isreadonly'=>$value['readonly'],
					'vdic'=>$value['dict'],
					'shareview'=>$value['share'],);

				$cellArray[]=$cell;
			}

			$dbcellret = $dbcell->InsertVDbcell($formId,$valueList['tid'],$cellArray);//创建字段信息
			
			if($dbcellret){
				$dbform->commit(); 
				return true;
			}else{
				$dbform->rollback(); 
				return false;
			}
		}

	}

	/**
     * 修改视图
     */
	public function UpdateViewForm($valueList,$vid){
		
		try{
			$dbform=new DbformModel();

			$dbform->startTrans();

			//创建视图s_dbform
			$dbForm=array($valueList['tid'],$valueList['vtype'],$valueList['vname'],$valueList['tname']);

			$resForm = $dbform->UpdateDbForm($dbForm,$vid);//编辑DbForm信息

			$dbcell=new VDbcellModel();

			foreach ($valueList['list'] as $key => $value) {
				$cell=array(
					'tname'=>$value['tname'],
					'cname'=>$value['cname'],
					'vtype'=>$valueList['vtype'],
					'indexorder'=>$value['order'],
					'ispk'=>$value['use'],
					'isnotnull'=>$value['create'],
					'isreadonly'=>$value['readonly'],
					'vdic'=>$value['dict'],
					'shareview'=>$value['share'],);

				$resCell = $dbcell->UpdateVDbcell($vid,$valueList['tid'],$value['tname'],$cell);//编辑视图信息
			}

			if($resForm || $resCell){
				$dbform->commit(); 
				return true;
			}else{
				$dbform->rollBack();
				return false;
			}

		}catch (Exception $exp) {
			$dbform->rollBack();
			return false;
		 }
	}
	
    /**
     * 删除视图
     */
	public function DelView($vid){
		
		$dbcell=new VDbcellModel();

        $dbcell->startTrans();

		if($dbcell->del($vid)){

			$dbform=new DbformModel();

			if($dbform->del($vid)){
				$dbform->commit(); 
				return true;
			}else{
				$dbform->rollback(); 
				return false;
			}
		}

	}

    /**
     * 通过试图ID和id获取信息
     */
	public function GetDataForVidandId($vid,$id){

		$dbform=new DbformModel();
        $formlist = $dbform->GetDBFormVid($vid);//获取表数据

        $dbtable=new TableModel();
		$data = $dbtable->GetData($formlist['form_table'],$id);//查询当前表单内容

		return $data;
	}

    /**
     * 生成HTML表单
     */
    public function CreateHtml($vid,$mid){

        $vdbcell=new VDbcellModel();
        $vdblist = $vdbcell->GetListForVid($vid);//获取视图字段

        $dbform=new DbformModel();
        $formlist = $dbform->GetDBFormVid($vid);//获取表数据

        $dbtable=new TableModel();
		$data = $dbtable->GetData($formlist['form_table'],$mid);//查询当前表单内容

        $arraylist=array("vid"=>$vid,"tid"=>$formlist['tid'],"vname"=>$formlist['vname'],"tname"=>$formlist['form_table']);

        $dbcell=new DbcellModel();
        $dblist = $dbcell->GetListForTid($formlist['tid']);//获取表字段

        $cells=array();
        foreach ($vdblist as $key => $value) {
            $fcd = GetTypeByName($value['vname'],$dblist)['fcd'];
            $flx = GetTypeByName($value['vname'],$dblist)['flx'];
            $cell=array(
                "index"=>$value['indexorder'],
                "vname"=>$value['vname'],
                "fnamec"=>$value['fnamec'],
                "isnotnull"=>$value['isnotnull'],
                "ispk"=>$value['ispk'],
                "isreadonly"=>$value['isreadonly'],
                "shareview"=>$value['shareview'],
                "vdic"=>$value['vdic'],
                "flx"=>$flx,
                "length"=> $fcd = '' ? 1000 : $fcd,
				"value"=>$data[$value['vname']],
            );
            $cells[]=$cell;
        }

        $arraylist['cells']=$cells;

        return extra_create_html($arraylist,$mid);
    }

    /**
     * 获取DBModel
     * @param int tid
     * @return array
     */
    public function GetDBModelForTid($tid){
        $dbmodel =new DbModel();
        return $dbid=$dbmodel->GetDBForID($tid);
    }
    
    /**
     * 获取DBForm视图列表
     * @param $tid 'tid'
     * @return $list 对应视图
     */
    public function GetTableView($tid){
        $db=new DbformModel();

        return $db->GetDBFormList($tid);
    }

    /**
     * 获取DBForm视图列表
     * @param $vid 'vid'
     *@return $list 对应视图
     */
	public function GetTableViewVid($vid){

        $db=new DbformModel();

        return $db->GetDBFormVid($vid);
    }


    /**
     * 通过$vid获取数据列表
     */
    public function GetListForVid($vid,$page_size,$where){
		
        $dbform=new DbformModel();

        $dbFormList = $dbform->GetDBFormVid($vid);//通过vid获取视图信息
		
        $view_column=$this->GetColumnForVid($vid);//获取列头

		$column='id';

		foreach ($view_column as $key => $value) {
			$column.=','.$value['vname'];
		}
        if ($dbFormList) {
            $dbtable=new TableModel();
            $tablelist = $dbtable->GetDataForTable($dbFormList['form_table'],$page_size,$column,$where);//传递表名、使用中文显示字段名，返回查询数据集
        } 

        return $tablelist;
    }

    /**
     * 通过$vid获取VDbcell列名
     */
    public function GetColumnForVid($vid){

        $dbcell=new VDbcellModel();

       return $dbcell->GetListForVid($vid);//通过vid获取视图信息

    }

    /**
     * 通过$tid获取列名
     */
    public function GetColumnFortid($tid){

        $dbcell=new DbcellModel();

       return $dbcell->GetListForTid($tid);//通过vid获取视图信息

    }

    /**
     * 通过字段名称，获取对应列
     */
    public function Getl_column_fname($name,$arraylist){
        return GetTypeByName($name,$arraylist);
    }

    
    /**
     * 插入数据
     * @param  string $datalist 数据集合
     */
    public function Insert_Form_Data($datalist){
        $db =new TableModel();
        return $db->Insert_Form_Data($datalist);
    }

	/**
	 * 更新数据
	 * @param  string $datalist 数据集合
	 */
	public function Update_Form_Data($datalist,$id){
        $db = new TableModel();
        return $db->Update_Form_Data($datalist,$id);
	}

	/**
	 * 删除数据
	 * @param  string $datalist 数据集合
	 */
	public function Del_Form_Data($vid,$mid){
        $db = new TableModel();
        return $db->Del_Form_Data($vid,$mid);
	}

	/**
	 * 获取所有共享字段
	 */
	public function Get_Share(){
		$db = new DbformModel();
		return $db->GetShare();
	}

	/**
	 * 获取所有字典内容
	 */
	public function Get_Dict(){

		$db = new DictModel();

		return $db->GetDict();
	}

}
