<?php
/**
 * Lottery for ThinkPHP的概率抽奖类
 * Created by PhpStorm.
 * User: gadfly
 * Date: 16/8/9
 * Time: 上午10:03
 *
#Lottery for ThinkPHP的概率抽奖类
配合数据库可以做到奖品总量限制和抽到奖品后该奖品的中奖概率自动降低

### 可选数组结构
```
$prize = array(
    '0' => array('id'=>1,'name'=>'平板电脑','probability'=>1),
	'1' => array('id'=>2,'name'=>'数码相机','probability'=>5),
	'2' => array('id'=>3,'name'=>'音箱设备','probability'=>10),
	'3' => array('id'=>4,'name'=>'4G优盘','probability'=>12),
	'4' => array('id'=>5,'name'=>'10Q币','probability'=>22),
);
```

### 可选数据库结构
单个奖品数量在1000以上时需要增加probability字段的小数位数，比如10000时需要将该字段设置成decimal(9,5)；同时触发器pre_lottery_before_upd_tr中的4也需要改成5.
```
DROP TABLE IF EXISTS `pre_lottery`;
CREATE TABLE `pre_lottery` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(50) NOT NULL COMMENT '奖品名称',
	`description` TEXT DEFAULT NULL COMMENT '奖品说明',
	`imgpath` varchar(255) DEFAULT NULL COMMENT '奖品图片路径',
	`ticket` decimal(11,2) DEFAULT '0.00' COMMENT '奖品价值金额',
	`total` int(11) DEFAULT '0' COMMENT '奖品发送总数',
	`probability` decimal(8,4) DEFAULT '100.0000' COMMENT '奖品获得概率',
	`dateline` int(11) DEFAULT '0' COMMENT '创建奖品的时间截',
	`status` smallint(6) DEFAULT '0' COMMENT '奖品状态, -1 --> 删除, 0 --> 正常',
	PRIMARY KEY (`id`),
	UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='抽奖奖品列表';

DROP TRIGGER IF EXISTS `pre_lottery_before_ins_tr`;
CREATE TRIGGER `pre_lottery_before_ins_tr` BEFORE INSERT ON `pre_lottery`
FOR EACH ROW
	BEGIN
		SET NEW.dateline = UNIX_TIMESTAMP(now());
	END;

DROP TRIGGER IF EXISTS `pre_lottery_before_upd_tr`;
CREATE TRIGGER `pre_lottery_before_upd_tr` BEFORE UPDATE ON `pre_lottery`
FOR EACH ROW
	BEGIN
		IF(NEW.total < OLD.total) THEN
			SET @pro = ROUND((OLD.total - NEW.total) / NEW.total, 4);
			SET NEW.probability = NEW.probability - @pro;
		ELSEIF(NEW.total > OLD.total) THEN
			SET @pro = ROUND((NEW.total - OLD.total) / NEW.total, 4);
			SET NEW.probability = NEW.probability + @pro;
		ELSE
			SET NEW.probability = OLD.probability;
		END IF;
	END;
```

### 数组方式使用方法
```
Vendor('Lottery');
$awards = array(
	'0' => array('id'=>1,'name'=>'平板电脑','probability'=>1),
	'1' => array('id'=>2,'name'=>'数码相机','probability'=>5),
	'2' => array('id'=>3,'name'=>'音箱设备','probability'=>10),
	'3' => array('id'=>4,'name'=>'4G优盘','probability'=>12),
	'4' => array('id'=>5,'name'=>'10Q币','probability'=>22),
);
$Lottery = new Lottery($awards);
dump($Lottery->roll());        // 常规算法 或者使用经典算法 dump($Lottery->roll('rand'));
```

### 数据库方式使用
```
Vendor('Lottery');
$Lottery = new Lottery('Lottery');
dump($Lottery->roll());        // 常规算法 或者使用经典算法 dump($Lottery->roll('rand'));
```

### 抽奖后的返回
```
array(5) {
	["errcode"] => int(0)
	["roll_key"] => int(3)
	["msg"] => string(12) "roll success"
	["prize"] => array(4) {        //此次抽奖中奖数据字段
		["id"] => int(4)
		["name"] => string(8) "4G优盘"
		["probability"] => int(12)
		["key"] => string(3) "yes"
	}
	["awards"] => array(5) {        // 此次抽奖未中奖数据字段
		[0] => array(4) {
			["id"] => int(6)
			["name"] => string(37) "明天再来没准就能中大奖哦!"
			["key"] => string(2) "no"
			["probability"] => int(50)
		}
		[1] => array(4) {
			["id"] => int(2)
			["name"] => string(12) "数码相机"
			["probability"] => int(5)
			["key"] => string(3) "yes"
		}
		[2] => array(4) {
			["id"] => int(3)
			["name"] => string(12) "音箱设备"
			["probability"] => int(10)
			["key"] => string(3) "yes"
		}
		[3] => array(4) {
			["id"] => int(1)
			["name"] => string(12) "平板电脑"
			["probability"] => int(1)
			["key"] => string(3) "yes"
		}
		[4] => array(4) {
			["id"] => int(5)
			["name"] => string(6) "10Q币"
			["probability"] => int(22)
			["key"] => string(3) "yes"
		}
	}
}
```
 *
 */
class Lottery {
	protected $awardsArr;
	protected $proField;
	protected $proSum = 0;
	protected $checkAward = false;
	protected $table = false;
	const SUCCESS_CODE = 0;
	const FAIL_CODE = -1;

	/**
	 * Lottery constructor.
	 *
	 * @param        $param         奖品设置数组或者奖品数据库表名
	 * @param string $probability   概率字段, 默认: probability
	 * @param string $noMsg         没有中奖的文字说明
	 */
	public function __construct($param, $probability='probability', $noMsg='明天再来没准就能中大奖哦!'){
		if(is_array($param)){
			$this->awardsArr = $param;
		}elseif(is_string($param)){
			$this->table = $param;
			$this->awardsArr = M($param)->where('status = 0')->select();
		}else{
			$this->failRoll('奖项数据不正确!');
		}
		$this->proField = $probability;
		$percentage = 0;
		foreach ($this->awardsArr as $key => $val) {
			$arr[$val['id']] = ($val['total'] == 0)?0:$val[$this->proField];
			$this->awardsArr[$key]['key'] = 'yes';
			$percentage = $percentage + $val[$this->proField];
			$id = $val['id'] + 1;
		}
		array_push($this->awardsArr, array('id' => $id, 'name' => $noMsg, 'key' => 'no', $this->proField => (100 - $percentage)));
		$this->checkAwards();
	}

	/**
	 * 检查抽奖数据
	 * @return bool
	 */
	protected function checkAwards(){
		if(!is_array($this->awardsArr) || empty($this->awardsArr)){
			return $this->checkAward = false;
		}
		$this->proSum = 0;
		foreach ($this->awardsArr as $_key => $award){
			$this->proSum += $award[$this->proField];
		}
		if(empty($this->proSum)){
			return $this->checkAward = false;
		}
		return $this->checkAward = true;
	}

	protected function successRoll($rollKey){
		if($this->table){
			if($this->awardsArr[$rollKey]['key'] == 'yes'){
				M($this->table)->where('id = '.$this->awardsArr[$rollKey]['id'])->setDec('total');
			}
		}
		$prize = $this->awardsArr[$rollKey];
		unset($this->awardsArr[$rollKey]); //将中奖项从数组中剔除，剩下未中奖项
		shuffle($this->awardsArr); //打乱数组顺序
		for($i=0;$i<count($this->awardsArr);$i++){
			$pr[] = $this->awardsArr[$i];
		}
		return array('errcode' => self::SUCCESS_CODE, 'roll_key' => $rollKey, 'msg' => '抽奖操作成功!', 'prize' => $prize, 'awards' => $pr);
	}

	protected function failRoll($msg = 'roll fail'){
		return array('errcode' => self::FAIL_CODE, 'msg' => $msg );
	}

	/**
	 * 经典概率算法
	 * @param $proArr
	 *
	 * @return int|string
	 */
	private function getrand($proArr) {
		$result = '';
		$proSum = array_sum($proArr);       //概率数组的总概率精度
		//概率数组循环
		foreach ($proArr as $key => $proCur) {
			$randNum = mt_rand(1, $proSum);
			if ($randNum <= $proCur) {
				$result = $key;
				break;
			} else {
				$proSum -= $proCur;
			}
		}
		unset ($proArr);
		return $result;
	}

	/**
	 * 抽奖
	 * @param   string  $type   算法默认是基础算法, base-->基础算法, rand-->经典算法
	 * @return  array
	 */
	public function roll($type='base') {
		if (false == $this->checkAward) {
			return $this->failRoll('奖品数据格式不正确!');
		}
		switch($type){
			case 'base':
				$result = mt_rand(0, $this->proSum);
				$proValue = 0;
				foreach ($this->awardsArr as $_key => $value) {
					$proValue += $value[$this->proField];
					if ($result <= $proValue) {
						return $this->successRoll($_key);
					}
				}
			case 'rand':
				foreach ($this->awardsArr as $key => $val) {
					$arr[$val['id']] = $val[$this->proField];
				}
				return $this->successRoll(($this->getrand($arr)-1));
		}
		return $this->failRoll('抽奖失败啦!');
	}
}