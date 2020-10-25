<?php
namespace workerbase\traits;
/**
 * @author fukaiyao
 */
trait Response{
	/**
	 * [show description]
	 * @param  integer $code   [状态码]
	 * @param  string $message [提示信息]
	 * @param  array  $data    [数据]
	 * @param  string $type    [数据类型]
	 * @return string         
	 */
	public function showResponse($code,$message='',$data=array(),$type="json"){
		if (!is_numeric($code)) {
		    return;
		}

		$type=isset($_GET['format'])?$_GET['format']:$type;
		$result=array(
				'code'=>$code,
				'message'=>$message,
				'data'=>$data
			);
		if ($type=='json') {
			$this->json($code,$message,$data);
			return;
		}elseif ($type=='arr') {
             var_export($result);
             die;
		}elseif ($type=='xml') {
            $this->xmlEncode($code,$message,$data);
            return;
		}else{
			//其他方法
		}

	}


	 /**
	  * [json description]
	 * @param  integer $code   [状态码]
	 * @param  string $message [提示信息]
	 * @param  array  $data    [数据]
	 * @return string    
	  */
	 public function json($code,$message='',$data=array()){
	 	if (!is_numeric($code)) {
				return ''; 		
	 	}
	 	$result=array(
	 			'code'=>$code,
	 			'message'=>$message,
	 			'data'=>$data
	 		);
         @header("Content-Type: text/json; charset=UTF-8");
         echo urldecode(json_encode($this->returnParams($result)));
	 }

	/**
	 * [xmlEncode description]
	 * @param  integer $code   [状态码]
	 * @param  string $message [提示信息]
	 * @param  array  $data    [数据]
	 * @return string         
	 */
	public function xmlEncode($code,$message='',$data=array()){
		if (!is_numeric($code)) {
				return '';
		}
		$result=array(
				'code'=>$code,
				'message'=>$message,
				'data'=>$data

			);
		header('Content-Type:text/xml');
		$xml ="<?xml version='1.0' encoding='UTF-8'?>\n";
		$xml.="<root>\n";
		$xml.=$this->xmlToEncode($result);
		$xml.="</root>";
		echo $xml;
	}
	/**
	 * [xmlToEncode description]
	 * @param  array $data [数据]
	 * @return string       
	 */
	public function xmlToEncode($data){
		$xml=$attr="";
		foreach ($data as $key => $value) {
			if (is_numeric($key)) {
				$attr=" id='{$key}'";
				$key="item";
			}
			$xml.="<{$key}{$attr}>";
			$xml.=is_array($value)?$this->xmlToEncode($value):$value;
			$xml.="</{$key}>\n";
		}
		return $xml;
	}

    /**
     * 返回经过地址加密的参数，用于支持json中文
     * @param $params
     * @param bool $tokey 应用到key
     * @return string
     */
    public function returnParams($params, $tokey = true)
    {
        if (is_array($params)) {
            foreach ($params as $key => $value){
                if (is_array($value)) {
                    $params[$key] = $this->returnParams($value);
                }
                else {
                    $params[$key] = urlencode($value);
                }
                if ($tokey && is_string($key)) {
                    $new_key = urlencode($key);

                    if ($new_key != $key) {
                        $params[$new_key] = $params[$key];
                        unset($params[$key]);
                    }
                }
            }
	} else {
            $params = urlencode($params);
        }

        return $params;
    }
}