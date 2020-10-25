<?php
namespace app\index\controller;
use app\common\controller\HomeBase;

require 'BaiduBce.phar';
require 'SampleConf.php';
include_once 'Auth.php';

// 好像没什么用
// use BaiduBce\BceClientConfigOptions;
// use BaiduBce\Util\Time;
// use BaiduBce\Util\MimeTypes;
// use BaiduBce\Http\HttpHeaders;
// use BaiduBce\Services\Bos\BosClient;
// use BaiduBce\Services\Bos\CannedAcl;
// use BaiduBce\Services\Bos\BosOptions;
// use BaiduBce\Auth\SignOptions;
// use BaiduBce\Log\LogFactory;
 
/**
 * 文件控制器
 */

class Baiduup extends  HomeBase
{
    public function index(){

    	$ak = "0afe18eca7764b559ce890a3e466f271";  // AccessKeyId
		$sk = "a063c249508e42e48fc7e0d4d7d2908a";  // SecretAccessKey

 		$BOS_TEST_CONFIG =array(
 			'credentials' => array('accessKeyId' => $ak,'secretAccessKey' => $sk,)
		    );   
		$bucketName = 'cnheneng';	

	   	if ($this->request->isPost()) {
	   		//上传百度BOS
    		$client = new BosClient($BOS_TEST_CONFIG);
    		//定义文件信息
	   		$objectKey = basename(input('savepath'));//上传后的文件名
	   		$fileName =  "./uploads/file/" . input('savepath');//上传后本地文件路径
	   		$fileid =  input('id');//文件的唯一识别码

	   		$res = $client->putObjectFromFile($bucketName, $objectKey, $fileName);
	   		if ($res->metadata['contentMd5']) {

	   			// $this->success('上传百度成功');
	   			//如果上传成功，开始把文档往百度DOC注册。
				//// 第一步：生成认证字符串
				$method = "POST";
				$host = "doc.bj.baidubce.com";
				$uri = "/v2/document";
				$params = array("source" => "bos");
				// date_default_timezone_set('UTC');
				$timestamp = new \DateTime();
				$expirationInSeconds = 1800;
				$authorization = generateAuthorization($ak, $sk, $method, $host, $uri, $params, $timestamp, $expirationInSeconds);

				// 第二步：构造HTTP请求的header、body等信息
				$url = "http://{$host}{$uri}?source=bos";
				$timeStr = $timestamp->format("Y-m-d\TH:i:s\Z");
				$head =  array(
				"Content-Type: application/json",
				"Authorization:{$authorization}",
				"x-bce-date:{$timeStr}",
				);
				$body = array(
				            "title" => $objectKey,
							//  "format" => "doc",
							"bucket" => "cnheneng", //BOS bucket 名称
							"object" => $objectKey, //Bucket中 文件路径
							// "targetType" => "h5",
							// "bosEndpoint" => "http://bj.bcebos.com"
							// "access" => "PUBLIC"
				);
				$bodyStr = json_encode($body);

				//第三步：发送HTTP请求，并输出响应信息。
				$curlp = curl_init();
				//curl_setopt($curlp, CURLOPT_POST, 1);
				curl_setopt($curlp, CURLOPT_URL, $url);
				curl_setopt($curlp, CURLOPT_CUSTOMREQUEST, $method); //定义请求类型，当然那个提交类型那一句就不需要了
				curl_setopt($curlp, CURLOPT_HTTPHEADER, $head);
				curl_setopt($curlp, CURLOPT_POSTFIELDS, $bodyStr);
				curl_setopt($curlp, CURLINFO_HEADER_OUT, 1);
				curl_setopt($curlp, CURLOPT_RETURNTRANSFER, 1);
				$response = curl_exec($curlp);
				$request = curl_getinfo($curlp, CURLINFO_HEADER_OUT);
				$status = curl_getinfo($curlp, CURLINFO_HTTP_CODE);
				curl_close($curlp);
				 
				if ($status == '200') {
					 $documentId = json_decode($response,TRUE);
					 $this->success('上传成功','',$documentId['documentId'],2);
					 
	               
				}else {
	                $this->error("上传失败!!");
				}
 

	   		}else{
	   			$this->error("上传失败!");
	   		}


	   	}else {
	   		$this->error("非法访问!");
	   	}
	 
	 
	 
   }


   //更新数据接口
   public function bdpost () {

   	if ($this->request->isPost()) {
   		$postdata = input('post.');
   		$postdata = json_decode($postdata,TRUE);
   		$postdata = json_decode($postdata['messageBody'],TRUE);
   		$data['baidustatus'] = $postdata['status'];
   		$data['pageid'] = $postdata['publishInfo']['pageCount'];
   		$up = db('doccon')->where('documentId',$postdata['documentId'])->update($data);
   		if ($up) {
   			 $this->success('更新成功');
   		}else {
   			$this->error("更新失败!");
   		}
   	}else {
   		$this->error("非法访问!");
   	}


   }


    
}