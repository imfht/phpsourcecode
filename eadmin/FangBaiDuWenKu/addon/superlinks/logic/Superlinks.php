<?php

namespace addon\superlinks\logic;
use app\common\model\ModelBase;

class Superlinks  extends ModelBase
{
    
   
    public static $superlinksModel = null;
    
    /**
     * 构造方法
     */
    public function __construct()
    {
        
    	 $class = get_addon_model ( 'superlinks',  'superlinks' );
            $model = new $class();
        
        self::$superlinksModel = $model;
    }
    
    public function getSuperlinksInfo($where = [], $field = true)
    {
    
    	return self::$superlinksModel->getInfo($where, $field);
    }
    

    public function superlinksAdd($data)
    {

        return self::$superlinksModel->setInfo($data) ? [RESULT_SUCCESS, '友情链接添加成功'] : [RESULT_ERROR, self::$superlinksModel->getError()];
    }

    public function setSuperlinksValue($where = [], $field = '', $value = '',$msg='')
    {
       
        return self::$superlinksModel->setFieldValue($where, $field, $value) ? [RESULT_SUCCESS, $msg] : [RESULT_ERROR, self::$superlinksModel->getError()];
    }

    public function superlinksAlldel($ids)
    {
    	

    return self::$superlinksModel->deleteAllInfo(['id'=>array('in',$ids)]) ? [RESULT_SUCCESS, '友情链接删除成功'] : [RESULT_ERROR, self::$superlinksModel->getError()];
    }  
    /**
     * 会员删除
     */
    public function superlinksDel($where = [])
    {
        
      
        
        return self::$superlinksModel->deleteInfo($where) ? [RESULT_SUCCESS, '友情链接删除成功'] : [RESULT_ERROR, self::$superlinksModel->getError()];
    }
}
