<?php
namespace wstmart\admin\model;
use wstmart\admin\validate\SupplierFlows as validate;
use wstmart\admin\validate\SupplierBase as ShopBaseValidate;
use think\Db;
/**
 * ============================================================================
 * WSTMart多用户商城
 * 版权所有 2016-2066 广州商淘信息科技有限公司，并保留所有权利。
 * 官网地址:http://www.wstmart.net
 * 交流社区:http://bbs.shangtao.net
 * 联系QQ:153289970
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 店铺入驻流程业务处理
 */
class SupplierFlows extends Base{
	protected $pk = 'flowId';
	/**
	 * 分页
	 */
	public function pageQuery(){
		return $this->where('dataFlag',1)->field(true)->order('sort asc')->paginate(input('limit/d'));
	}
	/**
	 * 列表
	 */
    public function listQuery(){
		return $this->where('dataFlag',1)->field(true)->select();
	}
	public function getById($id){
		return $this->get(['flowId'=>$id,'dataFlag'=>1]);
	}
	/**
	 * 新增
	 */
	public function add(){
		$data = input('post.');
		$data['createTime'] = date('Y-m-d H:i:s');
        $data['sort'] = (int)$data['sort'];
		WSTUnset($data,'flowId');
		Db::startTrans();
		try{
			$validate = new validate();
		    if(!$validate->scene('add')->check($data))return WSTReturn($validate->getError());
			$result = $this->allowField(true)->save($data);
			if(false !==$result){
		        if(false !== $result){
		        	Db::commit();
		        	return WSTReturn("新增成功", 1);
		        }
			}
		}catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn('新增失败',-1);	
	}
    /**
	 * 编辑
	 */
	public function edit(){
		$data = input('post.');
        $data['sort'] = (int)$data['sort'];
		WSTUnset($data,'createTime');
		Db::startTrans();
		try{
			$validate = new validate();
		    if(!$validate->scene('edit')->check($data))return WSTReturn($validate->getError());
		    $result = $this->allowField(true)->save($data,['flowId'=>(int)$data['flowId']]);
	        if(false !== $result){
	        	Db::commit();
	        	return WSTReturn("编辑成功", 1);
	        }
	    }catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn('编辑失败',-1);  
	}
	/**
	 * 删除
	 */
    public function del(){
	    $id = (int)input('post.id');
	    Db::startTrans();
		try{
		    $result = $this->where(['flowId'=>$id])->update(['dataFlag'=>-1]);
	        if(false !== $result){
                //删除其下的字段
                Db::name('supplier_bases')->where(['flowId'=>$id])->update(['dataFlag'=>-1]);
	        	Db::commit();
	        	return WSTReturn("删除成功", 1);
	        }
		}catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn('删除失败',-1); 
	}

    /**
     * 修改排序
     */
    public function changeSort(){
        $id = (int)input('id');
        $sort = (int)input('sort');
        $result = $this->where(['flowId'=>$id])->update(['sort'=>$sort]);
        if(false !== $result){
            WSTClearAllCache();
            return WSTReturn("操作成功", 1);
        }else{
            return WSTReturn($this->getError(),-1);
        }
    }

    /**
     * 显示是否显示/隐藏
     */
    public function editiIsShow(){
        $id = (int)input('post.id');
        $isShow = ((int)input('post.isShow')==1)?1:0;
        $result = $this->where(['flowId'=>$id])->update(['isShow' => $isShow]);
        if(false !== $result){
            WSTClearAllCache();
            return WSTReturn("操作成功", 1);
        }else{
            return WSTReturn($this->getError(),-1);
        }
    }

    /*
     * 获取流程下的字段
     */
    public function getFlowData(){
        $id = (int)input('id');
        $res = Db::name('supplier_bases')->where(['flowId'=>$id,'dataFlag'=>1])->select();
        return $res;
    }

    public function fieldPageQuery(){
        $flowId = (int)input('fId');
        $fieldName = input("fieldName");
        $dataType = input("dataType");
        $fieldTitle = input("fieldTitle");
        $isRequire = (int)input("isRequire");
        $fieldType = input("fieldType");
        $where = [];
        $where[] = ['dataFlag','=',1];
        $where[] = ['flowId','=',$flowId];
        if($fieldName != "")$where[] = ["fieldName","like","%".$fieldName."%"];
        if($dataType != -1)$where[] = ["dataType","=",$dataType];
        if($fieldTitle != "")$where[] = ["fieldTitle","like","%".$fieldTitle."%"];
        if($isRequire != -1)$where[] = ["isRequire","=",$isRequire];
        if($fieldType != -1)$where[] = ["fieldType","=",$fieldType];
        return Db::name('supplier_bases')->where($where)->paginate(input('limit/d'));
    }

    /*
     * 保存流程的字段
     */
    public function saveField(){
        $data = input('post.');
        $flowId = $data['flowId'];
        Db::startTrans();
        try{
            //判断字段名是否存在
            $prefix = config('database.prefix');
            $array = [
                'fieldName'=>$data['fieldName'],
                'dataType'=>$data['dataType'],
                'fieldTitle'=>$data['fieldTitle'],
                'dataLength'=>$data['dataLength'],
                'fieldSort'=>$data['fieldSort'],
                'isRequire'=>$data['isRequire'],
                'fieldComment'=>$data['fieldComment'],
                'fieldType'=>$data['fieldType'],
                'fieldAttr'=>$data['fieldAttr'],
            ];
            $array['fieldRelevance'] = '';
            $array['dateRelevance'] = '';
            $array['timeRelevance'] = '';
            $array['fileNum'] = '';
            $array['isShow'] = '';
            if(!empty($data['fieldRelevance']))$array['fieldRelevance'] = $data['fieldRelevance'];
            if(!empty($data['isRelevance']))$array['isRelevance'] = $data['isRelevance'];
            if(!empty($data['dateRelevance']))$array['dateRelevance'] = $data['dateRelevance'];
            if(!empty($data['timeRelevance']))$array['timeRelevance'] = $data['timeRelevance'];
            if(!empty($data['fileNum']))$array['fileNum'] = ((int)$data['fileNum']>1)?(int)$data['fileNum']:1;
            if(!empty($data['isShow']))$array['isShow'] = $data['isShow'];
            $validate = new ShopBaseValidate();
            if(!$validate->scene('edit')->check($array))return WSTReturn($validate->getError());
            $fieldName = $data['fieldName'];
            $dataType = $data['dataType'];
            $dataLength = $data['dataLength'];
            $fieldType = $data['fieldType'];
            $dataAttr = '';
            $default = '';
            switch ($dataType) {
                case 'date':
                    $dataAttr = "";
                    break;
                case 'time':
                    $dataAttr = "";
                    break;
                case 'decimal':
                    $dataAttr = "({$dataLength},0)";
                    break;
                default:
                    $dataAttr = "({$dataLength})";
                    break;
            }
            switch ($fieldType) {
                case 'radio':
                    $default = "DEFAULT 0";
                    break;
            }
            if($data['id']!=0){
                $old = Db::name('supplier_bases')->where(['id'=>$data['id'],'flowId'=>$flowId])->field('fieldName,isSuppliersTable,isDelete')->find();
                // 如果属于wst_suppliers表，则不允许修改字段与字段属性
                // 如果属于wst_extras表的基础字段，也不允许修改字段与字段属性
                // 排除以上两种情况，才允许修改字段与字段属性
                if($old['isSuppliersTable'] == 0 && $old['isDelete'] == 0){
                    // 如果修改了字段名称，则对supplier_extras表相应的字段进行改名，否则只修改字段属性
                    if($fieldName != $old['fieldName']){
                        $sql = "alter table `".$prefix."supplier_extras` change {$old['fieldName']} {$fieldName} {$dataAttr} {$default}";
                        Db::query($sql);
                    }else{
                        $sql = "alter table `".$prefix."supplier_extras` modify {$fieldName} {$dataType}{$dataAttr} {$default}";
                        Db::query($sql);
                    }
                }
                Db::name('supplier_bases')->where(['id'=>$data['id'],'flowId'=>$flowId])->update($array);
            }else{
                // 获取supplier_extras表的所有字段名称
                $fields = Db::query('show columns from '.$prefix.'supplier_extras');
                $fieldArray = [];
                foreach($fields as $v){
                    $fieldArray[] = $v['Field'];
                }
                // 判断添加的字段是否存在supplier_extras表，不存在则新增字段，存在则返回
                if(!in_array($fieldName,$fieldArray)){
                    $sql = "alter table `".$prefix."supplier_extras` add column {$fieldName} {$dataType}{$dataAttr} {$default}";
                    Db::query($sql);
                }else{
                    return WSTReturn('该字段已存在，请勿重复添加',-1);
                }
                $array['flowId'] = $flowId;
                $array['createTime'] = date('Y-m-d H:i:s');
                $array['dataFlag'] = 1;
                $array['isSuppliersTable'] = 0;
                $array['isDelete'] = 1;
                Db::name('supplier_bases')->insert($array);
            }

            Db::commit();
            return WSTReturn("保存成功", 1);
        }catch (\Exception $e){
            Db::rollback();
            return WSTReturn('保存失败'.$e->getMessage().$e->getLine(),-1);
        }
    }

    /*
     * 获取流程的某个字段详情
     */
    public function getFieldById($id){
        $field =  Db::name('supplier_bases')->where(['id'=>$id])->find();
        $field['fieldComment'] = htmlspecialchars_decode($field['fieldComment']);
        return $field;
    }

    /**
     * 删除流程的字段
     */
    public function delField(){
        $id = (int)input('post.id');
        Db::startTrans();
        try{
            $prefix = config('database.prefix');
            $result = Db::name('supplier_bases')->where(['id'=>$id])->update(['dataFlag'=>-1]);
            // 删除supplier_extras表相应的字段
            $fieldName = Db::name('supplier_bases')->where(['id'=>$id])->value('fieldName');
            $fields = Db::query('show columns from '.$prefix.'supplier_extras');
            $fieldArray = [];
            foreach($fields as $v){
                $fieldArray[] = $v['Field'];
            }
            if(in_array($fieldName,$fieldArray)){
                $sql = "alter table `".$prefix."supplier_extras` drop column {$fieldName}";
                Db::query($sql);
            }
            if(false !== $result){
                Db::commit();
                return WSTReturn("删除成功", 1);
            }
        }catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn('删除失败',-1);
    }

    /*
     * 获取单个入驻流程里的字段信息
     */
    public function getFlowFieldsById($id){
        if($id != -1){
            $rs = Db::name('supplier_bases')->where(['flowId'=>$id,'dataFlag'=>1])->order('fieldSort asc,id asc')->select();
            return $rs;
        }else{
            $rs = Db::name('supplier_bases')->where(['dataFlag'=>1])->whereNotIn('flowId',[2,3])->order('fieldSort asc,id asc')->select();
            return $rs;
        }
    }
}
