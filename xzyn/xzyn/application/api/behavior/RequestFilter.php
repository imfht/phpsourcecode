<?php
// 应用行为扩展定义文件

namespace app\api\behavior;

use app\common\model\ApiFields;
use app\common\model\ApiList as ApiLists;
use think\Validate;
use expand\ApiReturn;

class RequestFilter {

	// 输入参数过滤行为
    public function run($params)
    {
    	$hash = input('hash');
		$apiInfo = ApiLists::get( ['hash'=>$hash,'status'=>1] );
		if( $apiInfo['method'] == 1 ){	// post获取数据
			$data = request()->post();
			$method = 'POST';
		}elseif( $apiInfo['method'] == 2 ){	// get获取数据
			$data = request()->get();
			$method = 'GET';
		}else{
	        $method = strtoupper(request()->method());	// 用当前的请求方式 获取数据
	        switch ($method) {
	            case 'GET':
	                $data = request()->get();
	                break;
	            case 'POST':
	                $data = request()->post();
	                break;
	            case 'DELETE':
	                $data = request()->delete();
	                break;
	            case 'PUT':
	                $data = request()->put();
	                break;
	            default :
	                $data = [];
	                break;
	        }
		}
        if ( !empty($hash) ) {
            $rule = ApiFields::all(['hash' => $hash, 'type' => 0]);	//获取数据库的 请求字段
            $newRule = $this->buildValidateRule($rule);
            if ($newRule) {
                $validate = new Validate($newRule);
                if (!$validate->check($data)) {		//验证
                    return ApiReturn::r('-900',null, $validate->getError());	// 参数错误
                }
            }
            $newData = [];
            foreach ($rule as $item) {
            	if( empty($data[$item['fieldName']]) ){
            		$newData[$item['fieldName']] = $item['default'];
            	}else{
            		$newData[$item['fieldName']] = $data[$item['fieldName']];
            	}
                if (!$item['isMust'] && $item['default'] !== '' && !isset($data[$item['fieldName']])) {
                    $newData[$item['fieldName']] = $item['default'];
                }
            }
			cache('input_'.$hash,$newData, 5);	//请求的参数数据 缓存3秒
        }
    }

    /**
     * 将数据库中的规则转换成TP_Validate使用的规则数组
     * @param array $rule
     * @author 戏中有你 <admin@xzyn.cn>
     * @return array
     */
    public function buildValidateRule($rule = array()) {
        $newRule = [];
        if ($rule) {
            foreach ($rule as $value) {
                if ($value['isMust']) {
                    $newRule[$value['fieldName']][] = 'require';	//必填
                }
                switch ($value['dataType']) {
                    case 1:	//Integer[整数]
                        $newRule[$value['fieldName']][] = 'number';
                        if ($value['range']) {
                            $range = htmlspecialchars_decode($value['range']);
                            $range = json_decode($range, true);
                            if (isset($range['min'])) {
                                $newRule[$value['fieldName']]['egt'] = $range['min'];	// >= 大于等于
                            }
                            if (isset($range['max'])) {
                                $newRule[$value['fieldName']]['elt'] = $range['max'];	// <= 小于等于
                            }
                        }
                        break;
                    case 2:	//String[字符串]
                        if ($value['range']) {
                            $range = htmlspecialchars_decode($value['range']);
                            $range = json_decode($range, true);
                            if (isset($range['min'])) {
                                $newRule[$value['fieldName']]['min'] = $range['min'];	//最小长度
                            }
                            if (isset($range['max'])) {
                                $newRule[$value['fieldName']]['max'] = $range['max'];	//最大长度
                            }
                        }
                        break;
                    case 3:	//Boolean[布尔]
                    	$newRule[$value['fieldName']][] = 'boolean';
                        break;
                    case 4:	//Enum[枚举]
                        if ($value['range']) {
                            $range = htmlspecialchars_decode($value['range']);
                            $range = json_decode($range, true);
                            $newRule[$value['fieldName']]['in'] = implode(',', $range);
                        }
                        break;
                    case 5:	//Float[浮点数]
                        $newRule[$value['fieldName']][] = 'float';
                        if ($value['range']) {
                            $range = htmlspecialchars_decode($value['range']);
                            $range = json_decode($range, true);
                            if (isset($range['min'])) {
                                $newRule[$value['fieldName']]['egt'] = $range['min'];	// >= 大于等于
                            }
                            if (isset($range['max'])) {
                                $newRule[$value['fieldName']]['elt'] = $range['max'];	// <= 小于等于
                            }
                        }
                        break;
                    case 6:	//File[文件]
                    	$newRule[$value['fieldName']][] = 'image';
                        break;
                    case 7:	//Mobile[手机号]
                        $newRule[$value['fieldName']][] = 'mobile';
                        break;
                    case 9:	//Array[数组]
                        $newRule[$value['fieldName']][] = 'array';
                        if ($value['range']) {
                            $range = htmlspecialchars_decode($value['range']);
                            $range = json_decode($range, true);
                            if (isset($range['min'])) {
                                $newRule[$value['fieldName']]['min'] = $range['min'];	//最小长度
                            }
                            if (isset($range['max'])) {
                                $newRule[$value['fieldName']]['max'] = $range['max'];	//最大长度
                            }
                        }
                        break;
                    case 10:	//Email[邮箱]
                        $newRule[$value['fieldName']][] = 'email';
                        break;
                    case 11:	//Date[日期]
                        $newRule[$value['fieldName']][] = 'date';
                        break;
                    case 12:	//Url
                        $newRule[$value['fieldName']][] = 'url';
                        break;
                    case 13:	//IP
                        $newRule[$value['fieldName']][] = 'ip';
                        break;
					default:
						$newRule[$value['fieldName']][] = '';
                }
            }
        }
        return $newRule;
    }

}
