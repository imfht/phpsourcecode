<?php
/**
 * 页面类
 *
 * 用于前端的数据调用
 *
 * @author		黑冰(001.black.ice@gmail.com)
 * @copyright	(c) 2012
 * @version		$Id$
 * @package		page
 * @since		v0.1
 */

class Page{
	
	/**
	 * 用途:获取数据(多条)
	 *
	 * 示例:Page::getContentByList('whoneed_article', 'where type=8 order by id desc');
	 * 
	 * @author	黑冰(001.black.ice@gmail.com)
	 * @param	string	$table_name	表名
	 * @param	string	$condition	查询条件 如：where, order by ,gropu by 
	 * @param	string	$select		待查询字段			默认为'*'
	 * @param	int		$limit		查询的记录条数		默认为20
	 * @param	boolean	$page		是否需要计算分页	默认为true
	 *
	 * @return	array
	 * array
	 */
	static function getContentByList($table_name = '', $condition = '', $select = '*', $limit = 20, $page = true, $objConn = null){
		$arrR = array();
		
		// 表名不能为空
		if($table_name){

			$intBeginLimit = 0;			// limit begin
			$intEndLimit   = $limit;	// limit end
			
			// 分页 limit 处理
			if($page){				
				$intPage = intval($_GET['page']);
				if(!$intPage) $intPage = 1;
				
				$intBeginLimit = ($intPage - 1) * $limit;
			}
			
			// 拼凑limit数据
			$strLimit = '';
			if($limit){
				$strLimit = "limit {$intBeginLimit},{$intEndLimit}";
			}

			$strSql = "select {$select} from {$table_name} {$condition} {$strLimit}";
			
			$conn	= null;
			if($objConn) $conn = $objConn;
			else $conn = Yii::app()->db;
			$arrR['data'] = $conn->createCommand($strSql)->queryAll();
			$arrR['page'] = '';

			// 分页 pagelink 处理
			if($page){
				$strSql = "select count(*) as c from {$table_name} {$condition}";
				$arrT	= $conn->createCommand($strSql)->queryRow();
				$count	= $arrT['c'];

				$strPage = MyFunction::funGetPage($count, $intPage, $limit);
				$arrR['page'] = $strPage;
			}		
		}

		return $arrR;	
	}

	/**
	 * 用途:获取数据(单条)
	 *
	 * 示例:Page::getContentByOne('whoneed_article', 'where id = 1', 'id');
	 * 
	 * @author	黑冰(001.black.ice@gmail.com)
	 * @param	string	$table_name	表名
	 * @param	string	$condition	查询条件 如：where, order by ,gropu by 
	 * @param	string	$select		待查询字段			默认为'*'
	 *
	 * @return	array
	 * array
	 */
	static function getContentByOne($table_name = '', $condition = '', $select = '*', $objConn = null){
		$arrR = array();
		
		// 表名不能为空
		if($table_name){
			
			$strSql = "select {$select} from {$table_name} {$condition} limit 1";

			$conn	= null;
			if($objConn) $conn = $objConn;
			else $conn = Yii::app()->db;

			$arrR = $conn->createCommand($strSql)->queryRow();		
		}

		return $arrR;	
	}

	// 得到某个表的指定内容(sql)
	static function funGetIntroBySql($sql = '', $isOne = false, $objConn = null){
		
		$arrR = array();

		if($sql){
			$conn	= null;
			if($objConn) $conn = $objConn;
			else $conn = Yii::app()->db;
			
			if($isOne){
				$arrR	= $conn->createCommand($sql)->queryRow();
			}else{
				$arrR	= $conn->createCommand($sql)->queryAll();
			}
		}

		return $arrR;	
	}

	// 得到指定表的数据列表
	static function funGetIntro($model_name = '', $where = '', $select = '*', $limit = 20){
		
		$arrR = array();
		$model_name = ucfirst($model_name);
		$model		= new $model_name();

		// 过滤条件
		$cdb = new CDbCriteria();
		if($select)	$cdb->select	= $select;
		if($where)	$cdb->condition	= $where;
		$cdb->order		= "id desc";

		//分页
		$intPage = intval($_GET['page']);
		if(!$intPage) $intPage = 1;
		
		// 总条数
		$count = $model->count($cdb);

		$pages = new CPagination($count);
		$pages->pageSize	= $limit;
		$pages->currentPage = $intPage - 1;
		$pages->applyLimit($cdb);
		
		// 取得分页数据
		$objData = $model->findAll($cdb);

		// 分页代码
		$strPage = MyFunction::funGetPage($count, $intPage, $limit);
		
		$arrR['data'] = $objData;
		$arrR['page'] = $strPage;

		return $arrR;
	}

	// 得到指定表的数据
	static function funGetIntroOneObj($model_name = '', $where = '', $select = '*'){
		$arrR = array();
		$model_name = ucfirst($model_name);
		$model		= new $model_name();

		// 过滤条件
		$cdb = new CDbCriteria();
		if($select)	$cdb->select	= $select;
		if($where)	$cdb->condition	= $where;
		$cdb->order = 'id desc';
		$cdb->limit	= 1;

		// 取得数据
		$objData = $model->find($cdb);

		return $objData;	
	}

	// 生成列表链接
	static function funGetListLink($lid = 0, $sid = 0){
		$lid = intval($lid);
		$sid = intval($sid);

		$strUrl = "/list_{$lid}_{$sid}.html";

		return $strUrl;
	}

	// 生成课程详细页
	static function funGetDetailLink($lid = 0, $sid = 0, $cid = 0){
		$lid = intval($lid);
		$sid = intval($sid);

		$strUrl = "/detail_{$lid}_{$sid}_{$cid}.html";

		return $strUrl;
	}

	/**
	 * 用途: 返回文件路径 (引入公共文件,需要自动include)
	 *
	 * 示例: Page::importFile('room_title.php');
	 * 
	 * @author	黑冰(001.black.ice@gmail.com)
	 * @param	string	$file_name	待引入的文件名称
	 * @param	string	$file_path	文件路径；默认  /view/common_layouts/
	 *
	 * @return	string
	 * string
	 */
	static function importFile($file_name, $file_path = '/common_layouts/'){
		$strFile = '';

		if($file_name){
			$strFilePath = APP_ROOT.'/protected/views'.$file_path.$file_name;
			if(file_exists($strFilePath)){
				$strFile = $strFilePath;
			}
		}

		return $strFile;
	}

    static function funGetImg($strImg = '')
    {   
        if($strImg)
            return Yii::app()->params['img_domain'].$strImg;
        else
            return '';
    }
}
