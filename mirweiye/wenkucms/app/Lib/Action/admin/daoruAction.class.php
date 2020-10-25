<?php
 
class daoruAction extends frontendAction
{
    
 
    public function index()
    {
    	 
    	$data = M('ds_docinfo')->select();

    	foreach ($data as $k => $v) {

    		$info['id'] = $v['docid'];
			$info['cateid'] = $v['doctype'];
			$info['title'] = $v['title'];

			$info['intro'] = $v['short'];
			$info['ext'] = substr(strrchr(basename($v['docpath']), '.'), 1);
			$info['filesize'] = $v['docsize'];
			$info['filename'] = basename($v['docpath']);
		 
			$info['oldname'] = basename($info['filename'],'.'.$info['ext']);  
			$info['filename'] = $info['oldname'];

			$info['fileurl'] = $info['ext'] .'/'.basename($v['docpath']); //ddd
		 	$info['hash'] = MD5($info['oldname']);
			$info['uid'] = '1';
			$info['hits'] = rand(120,888);
			$info['add_time'] = strtotime($v['uploaddate']);
			$info['model'] = '3';

			// if (M('doc_con')->add($info)) {
			// 	echo "插入" . $info['title'] . " <span style='color:green'>成功</span><br/>";
			// }else {
			// 	echo "插入" . $info['title'] . " <span style='color:red'>失败</span><br/>";
			// } 

    		echo $info['ext'] . "<br>";
    	}

    	// print_r($info);
    }
    
     

}