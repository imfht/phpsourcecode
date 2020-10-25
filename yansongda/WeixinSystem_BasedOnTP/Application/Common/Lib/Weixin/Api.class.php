<?php
namespace Common\Lib\Weixin;

/**
* 微信API 包括常用的功能（快递、天气等）
* --------------------------
* @author JasonYan <yansong.da@qq.com>
* --------------------------
* lastmodified : 2014-10-10
*/
class Api
{
	/**
	 * 快递查询处理
	 * @param  [type] $content [内容]
	 * @return [type]          [description]
	 */
	public function express($content)
	{
		$expresses = array (
            '德邦' => 'debang',
            'ems' => 'ems',
            '邮政' => 'gnxb',
            '国通' => 'guotong',
            '汇通' => 'huitong',
            '邮政平邮' => 'pingyou',
            '如风达' => 'rufeng',
            '申通' => 'shentong',
            '顺丰' => 'shunfeng',
            '天天' => 'tiantian',
            'ups' => 'UPS',
            'usps' => 'USPS',
            '圆通' => 'yuantong',
            '韵达' => 'yunda',
            '宅急送' => 'zhaijisong',
            '中铁' => 'zhongtie',
            '中通' => 'zhongtong',
        );
        if ( strstr($content, '！') ) {
        	$info = explode('！', $content);
        } elseif ( strstr($content, '!') ) {
        	$info = explode('!', $content);
        } else {
        	return array(
        		'MsgType' => 'text',
	            'Content' => '您输入的格式有误，请重新输入：',
        	);
        }
		
		$com = $info['0'];$num = $info['1'];
		if ( $expresses[$com] ) {
			$com = $expresses[$com];
			$kdurl = "http://api.ickd.cn/?id=".C('id')."&secret=".C('secret')."&com=".urlencode($com)."&nu=".urlencode($num)."&type=json&encode=utf8&ord=desc";              
	        $json_jieguo = file_get_contents($kdurl);
	        $jieguo = json_decode($json_jieguo,true);
	        $statusb = $jieguo['status'];
	        $statusarr = array("0" =>"查询失败","1"=>"正常","2"=>"派送中","3"=>"已签收","4"=>"退回","5"=>"其他问题",);
	        $status = $statusarr[$statusb];
	        
            $bdata = $jieguo['data'];
            if( $jieguo['status'] != 0 && $jieguo['errCode'] == 0){
                foreach ($bdata as $value) {
                    $zt .= "时间：{$value['time']} , 状态：{$value['context']}\n";
                }
                return array(
					'MsgType' => 'text',
	                'Content' => $jieguo['expTextName'].":".$jieguo['mailNo']."，".$status.":\n".$zt,
                ); 
            } else{
            	$errcodeb = $jieguo['errCode'];
		        $errcodearr = array("0"=>"无错误","1"=>"单号不存在","2"=>"验证码错误","3"=>"链接查询服务器失败","4"=>"程序内部错误","5"=>"程序执行错误","6"=>"快递单号格式错误","7"=>"快递公司错误","10"=>"未知错误",);
		        $errcode = $errcodearr[$errcodeb];
            	return array(
					'MsgType' => 'text',
	                'Content' => $jieguo['expTextName'].":".$jieguo['mailNo']."，".$status."。\n可能的原因是：".$errcode."。\n建议的操作是：".$jieguo['message'],
                ); 
            }
		} else {
			return array(
                'MsgType' => 'text',
                'Content' => '您输入的快递公司暂时不支持，后续会提供相应支持！感谢您的使用！请重新输入相关指令。'
            );
		}
	}

	/**
	 * 获取宜昌地区的天气
	 * @return [type] [description]
	 */
	public function weather()
	{
		$tq = file_get_contents("http://m.weather.com.cn/data/101200901.html");
        $jg = json_decode($tq,true);
        $info = $jg['weatherinfo'];           
        return array(
        	'MsgType' => 'text',
        	'Content' => "{$info['date_y']}{$info['week']},{$info['city']}的天气情况\n今天:({$info['temp1']}){$info['weather1']}{$info['wind1']}{$info['fl1']}。24小时穿衣指数:{$info['index_d']}\n明天:({$info['temp2']}){$info['weather2']}{$info['wind2']}{$info['fl2']}。48小时穿衣指数:{$info['index48_d']}"
        );
	}

	/**
	 * 获取翻译结果
	 * @param  [type] $content [description]
	 * @return [type]          [description]
	 */
	public function translate($content)
	{
		$url = "http://fanyi.youdao.com/openapi.do?keyfrom=yanda-weixin&key=1770310156&type=data&doctype=json&version=1.1&q=".urlencode($content);
        $fy = file_get_contents($url);
        $fyjg = json_decode($fy,true);
        return array(
            'MsgType' => 'text',
            'Content' => "基本翻译：{$fyjg['translation']['0']}\n详细翻译：{$fyjg['basic']['phonetic']}\n{$fyjg['basic']['explains']['0']},{$fyjg['basic']['explains']['1']}"
        );
	}
	
}