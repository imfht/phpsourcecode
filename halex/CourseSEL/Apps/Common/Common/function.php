<?php
/**º¯Êý¿â
 * @Author: hxb0810
 /**
 * EXCEL 导入导出
 * auther: yuan
 * date:2016-10-31 17:05
 */
/**
+----------------------------------------------------------
 * Export Excel | 2013.08.23
 * Author:HongPing <hongping626@qq.com>
+----------------------------------------------------------
 * @param $expTitle     string File name
+----------------------------------------------------------
 * @param $expCellName  array  Column name
+----------------------------------------------------------
 * @param $expTableData array  Table data
+----------------------------------------------------------
 */
function exportExcel0($expTitle,$expCellName,$expTableData){
    $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//ÎÄ¼þÃû³Æ
    $fileName = $_SESSION['loginAccount'].date('_YmdHis');//or $xlsTitle ÎÄ¼þÃû³Æ¿É¸ù¾Ý×Ô¼ºÇé¿öÉè¶¨
    $cellNum = count($expCellName);
    $dataNum = count($expTableData);
    vendor("PHPExcel.PHPExcel");
    $objPHPExcel = new PHPExcel();
    $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

    $objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//ºÏ²¢µ¥Ôª¸ñ
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'  Export time:'.date('Y-m-d H:i:s'));
    for($i=0;$i<$cellNum;$i++){
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'2', $expCellName[$i][1]);
    }
    // Miscellaneous glyphs, UTF-8
    for($i=0;$i<$dataNum;$i++){
        for($j=0;$j<$cellNum;$j++){
            $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), $expTableData[$i][$expCellName[$j][0]]);
        }
    }

    header('pragma:public');
    header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
    header("Content-Disposition:attachment;filename=$fileName.xls");//attachmentÐÂ´°¿Ú´òÓ¡inline±¾´°¿Ú´òÓ¡
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
}
//转换为一维数组

function i_array_column($input, $columnKey, $indexKey=null){
    if(!function_exists('array_column')){ 
        $columnKeyIsNumber  = (is_numeric($columnKey))?true:false; 
        $indexKeyIsNull            = (is_null($indexKey))?true :false; 
        $indexKeyIsNumber     = (is_numeric($indexKey))?true:false; 
        $result                         = array(); 
        foreach((array)$input as $key=>$row){ 
            if($columnKeyIsNumber){ 
                $tmp= array_slice($row, $columnKey, 1); 
                $tmp= (is_array($tmp) && !empty($tmp))?current($tmp):null; 
            }else{ 
                $tmp= isset($row[$columnKey])?$row[$columnKey]:null; 
            } 
            if(!$indexKeyIsNull){ 
                if($indexKeyIsNumber){ 
                  $key = array_slice($row, $indexKey, 1); 
                  $key = (is_array($key) && !empty($key))?current($key):null; 
                  $key = is_null($key)?0:$key; 
                }else{ 
                  $key = isset($row[$indexKey])?$row[$indexKey]:0; 
                } 
            } 
            $result[$key] = $tmp; 
        } 
        return $result; 
    }else{
        return array_column($input, $columnKey, $indexKey);
    }
}
//////判断是否md5
function is_md5($p){
	return preg_match("/^[a-z0-9]{32}$/",$p);
}
//分页函数
function getpage($table,$perpage){
    $l=D($table);
            $count      = $l->count();
            $Page       = new \Think\Page($count,$perpage);
            $Page->setConfig('prev',  '<span >ÉÏÒ»Ò³</span>');//ÉÏÒ»Ò³
            $Page->setConfig('next',  '<span >ÏÂÒ»Ò³</span>');//ÏÂÒ»Ò³
            $Page->setConfig('first', '<span >Ê×Ò³</span>');//µÚÒ»Ò³
            $Page->setConfig('last',  '<span >Î²Ò³</span>');//×îºóÒ»Ò³
            $show=$Page->show();// ·ÖÒ³ÏÔÊ¾Êä³ö
            $llist = $l->order('tid desc')->limit($Page->firstRow.','.$Page->listRows)->select();
            $data['page']=$show;
            $data['data']=$llist;
            return $data;
}
//学校函数
function getschool($schoolid){
		switch ($schoolid) {
			case '1':
				return '孝义中学';
				break;
			case '2':
				return '孝义二中';
				break;
			case '3':
				return '孝义三中';
				break;
			case '4':
				return '孝义四中';
				break;
			case '5':
				return '孝义五中';
				break;
			case '6':
				return '孝义实验中学';
				break;
			case '7':
				return '孝义华杰中学';
				break;
			case '8':
				return '孝义艺苑中学';
				break;
            case '0':
                return '教育局';
                break;
	}
}
//学校函数结束
//获取权限函数
function get_type($type){
        switch ($type) {
            case '0':
                return '管理员（班主任）';
                break;
            case '1':
                return '学校管理员';
                break;
            case '2':
                return '超级管理员';
                break;
    }
}
//验证复选框是否选中
function check_checkbox($value,$arr){
    if(in_array($value, $arr)){ 
        echo 'checked="checked"';
        } 

}
//字符串转数组
function ch2arr($str)
{
    $length = mb_strlen($str, 'utf-8');
    //$array = [];
    for ($i=0; $i<$length; $i++)  
        $array[] = mb_substr($str, $i, 1, 'utf-8');    
    return $array;
}
//获取各个选课项目的百分比
function get_percent($seid,$schoolid){
    $re=M('sel')->where('seid='.$seid)->find();
    $where['seid']=$seid;
    $map['year']=$where['year']=$re['year'];
    $map['schoolid']=$re['schoolid'];
    if (!empty($schoolid)) {
        $where['schoolid']=$schoolid;
    }
    $countstu=M('stu')->where($map)->count();
    //dump ($countstu);
    $countstusel=M('selstu')->where($where)->count();
    //dump($countstusel);
    $a=intval($countstu);
    $b=intval($countstusel);
    //echo $a;
    $res=($b/$a)*100;
    //echo $res;
    
    $data['title']= $countstusel.'/'.$countstu.'='.$res.'%';
    $data['percent'] =$res.'%';
    return $data;

}