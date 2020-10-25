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