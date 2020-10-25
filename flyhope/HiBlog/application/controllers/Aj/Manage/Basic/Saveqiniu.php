<?php
/**
 * 保存七牛配置
 *
 * @author chengxuan <i@chengxuan.li>
 */
class Aj_Manage_Basic_SaveqiniuController extends Aj_AbsController {

	public function indexAction() {
		$qiniu_ak = Comm\Arg::post('qiniu-ak');
		$qiniu_sk = Comm\Arg::post('qiniu-sk');
		$qiniu_domain = Comm\Arg::post('qiniu-domain');
		$qiniu_bucket = Comm\Arg::post('qiniu-bucket');
		
		Model\Blog::save(array(
			'qiniu-ak' => $qiniu_ak,
		    'qiniu-sk' => $qiniu_sk,
		    'qiniu-domain' => $qiniu_domain,
		    'qiniu-bucket' => $qiniu_bucket,
		));
		
		Comm\Response::json(100000, '操作成功', null, false);
	}
	
}
