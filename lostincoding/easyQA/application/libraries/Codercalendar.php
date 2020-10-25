<?php

/**
 * 程序员老黄历
 * 原作者：https://github.com/gracece
 * 原Github地址：https://github.com/gracece/coder-calendar
 */
class Codercalendar
{
    private $now = null;
    private $today = null;
    private $iday = null;
    private $weeks = null;
    private $directions = null;
    private $activities = null;
    private $varNames = null;
    private $tools = null;
    private $drinks = null;

    //构造方法
    public function __construct()
    {
        $this->now = date('Y-m-d H:i:s');
        $this->today = getdate();
        $this->iday = $this->today['year'] * 10000 + $this->today['mon'] * 100 + $this->today['mday'];
        $this->weeks = array("日", "一", "二", "三", "四", "五", "六");
        $this->directions = array("北方", "东北方", "东方", "东南方", "南方", "西南方", "西方", "西北方");
        $this->activities = array(
            array('name' => "写单元测试", 'good' => "写单元测试将减少出错", 'bad' => "写单元测试会降低你的开发效率"),
            array('name' => "洗澡", 'good' => "你几天没洗澡了？", 'bad' => "会把设计方面的灵感洗掉", 'weekend' => true),
            array('name' => "锻炼一下身体", 'good' => "", 'bad' => "能量没消耗多少，吃得却更多", 'weekend' => true),
            array('name' => "抽烟", 'good' => "抽烟有利于提神，增加思维敏捷", 'bad' => "除非你活够了，死得早点没关系", 'weekend' => true),
            array('name' => "白天上线", 'good' => "今天白天上线是安全的", 'bad' => "可能导致灾难性后果"),
            array('name' => "重构", 'good' => "代码质量得到提高", 'bad' => "你很有可能会陷入泥潭"),
            array('name' => "使用%t", 'good' => "你看起来更有品位", 'bad' => "别人会觉得你在装逼"),
            array('name' => "跳槽", 'good' => "该放手时就放手", 'bad' => "鉴于当前的经济形势，你的下一份工作未必比现在强"),
            array('name' => "招人", 'good' => "你面前这位有成为牛人的潜质", 'bad' => "这人会写程序吗？"),
            array('name' => "面试", 'good' => "面试官今天心情很好", 'bad' => "面试官不爽，会拿你出气"),
            array('name' => "提交辞职申请", 'good' => "公司找到了一个比你更能干更便宜的家伙，巴不得你赶快滚蛋", 'bad' => "鉴于当前的经济形势，你的下一份工作未必比现在强"),
            array('name' => "申请加薪", 'good' => "老板今天心情很好", 'bad' => "公司正在考虑裁员"),
            array('name' => "撸管", 'good' => "避免缓冲区溢出", 'bad' => "强撸灰飞烟灭", 'weekend' => true),
            array('name' => "晚上加班", 'good' => "晚上是程序员精神最好的时候", 'bad' => "", 'weekend' => true),
            array('name' => "在妹子面前吹牛", 'good' => "改善你矮穷挫的形象", 'bad' => "会被识破", 'weekend' => true),
            array('name' => "浏览成人网站", 'good' => "重拾对生活的信心", 'bad' => "你会心神不宁", 'weekend' => true),
            array('name' => "命名变量%v", 'good' => "", 'bad' => ""),
            array('name' => "写超过%l行的方法", 'good' => "你的代码组织的很好，长一点没关系", 'bad' => "你的代码将混乱不堪，你自己都看不懂"),
            array('name' => "提交代码", 'good' => "遇到冲突的几率是最低的", 'bad' => "你遇到的一大堆冲突会让你觉得自己是不是时间穿越了"),
            array('name' => "代码复审", 'good' => "发现重要问题的几率大大增加", 'bad' => "你什么问题都发现不了，白白浪费时间"),
            array('name' => "开会", 'good' => "写代码之余放松一下打个盹，有益健康", 'bad' => "小心被扣屎盆子背黑锅"),
            array('name' => "打DOTA", 'good' => "你将有如神助", 'bad' => "你会被虐的很惨", 'weekend' => true),
            array('name' => "晚上上线", 'good' => "晚上是程序员精神最好的时候", 'bad' => "你白天已经筋疲力尽了"),
            array('name' => "修复BUG", 'good' => "你今天对BUG的嗅觉大大提高", 'bad' => "新产生的BUG将比修复的更多"),
            array('name' => "设计评审", 'good' => "设计评审会议将变成头脑风暴", 'bad' => "人人筋疲力尽，评审就这么过了"),
            array('name' => "需求评审", 'good' => "", 'bad' => ""),
            array('name' => "上微博", 'good' => "今天发生的事不能错过", 'bad' => "今天的微博充满负能量", 'weekend' => true),
            array('name' => "上AB站", 'good' => "还需要理由吗？", 'bad' => "满屏的兄贵我会说出来？", 'weekend' => true),
        );
        $this->varNames = array("jieguo", "huodong", "pay", "expire", "zhangdan", "every", "free", "i1", "a", "virtual", "ad", "spider", "mima", "pass", "ui");
        $this->tools = array("Eclipse写程序", "MSOffice写文档", "记事本写程序", "Windows8", "Linux", "MacOS", "IE", "Android设备", "iOS设备");
        $this->drinks = array("水", "茶", "红茶", "绿茶", "咖啡", "奶茶", "可乐", "牛奶", "豆奶", "果汁", "果味汽水", "苏打水", "运动饮料", "酸奶", "酒");
    }

    public function showLucky()
    {
        $result = array();
        $result['today'] = date("Y年m月d日 星期" . $this->weeks[date('w')]);
        $_activities = $this->filter($this->activities);
        $numGood = $this->random($this->iday, 98) % 3 + 2;
        $numBad = $this->random($this->iday, 87) % 3 + 2;
        $eventArr = $this->pickRandomActivity($_activities, $numGood + $numBad);

        //宜
        for ($i = 0; $i < $numGood; $i++) {
            $result['good_lists'][$i][] = $eventArr[$i]['name'];
            $result['good_lists'][$i][] = $eventArr[$i]['good'];
        }

        //不宜
        for ($i = 0; $i < $numBad; $i++) {
            $result['bad_lists'][$i][] = $eventArr[$i + $numGood]['name'];
            $result['bad_lists'][$i][] = $eventArr[$i + $numGood]['bad'];
        }

        //座位朝向
        $result['direction'] = $this->directions[$this->random($this->iday, 2) % sizeof($this->directions)];
        //今日宜饮
        $todayDrink = $this->pickRandom($this->drinks, 2);
        $result['todayDrink_lists'][] = $todayDrink[0];
        $result['todayDrink_lists'][] = $todayDrink[1];
        //女神亲近指数
        $star_num = $this->random($this->iday, 6) % 5 + 1;
        $result['star_num'] = $star_num;
        $result['star'] = $this->star($star_num);
        return $result;
    }

    private function random($dayseed, $indexseed)
    {
        $n = $dayseed % 11117;
        for ($i = 0; $i < 100 + $indexseed; $i++) {
            $n = $n * $n;
            $n = $n % 11117;
        }
        return $n;
    }

    private function star($num)
    {
        $result = "";
        $i = 0;
        while ($i < $num) {
            $result .= "★";
            $i++;
        }
        while ($i < 5) {
            $result .= "☆";
            $i++;
        }
        return $result;
    }

    private function filter($activities)
    {
        $result = array();
        if ($this->isWeekend()) {
            foreach ($activities as $value) {
                if (isset($value['weekend']) && $value['weekend']) {
                    $result[] = $value;
                }

            }
            return $result;
        }
        return $activities;

    }

    private function isWeekend()
    {
        $today = getdate();
        return $today['wday'] == 6 || $today['wday'] == 7;
    }

    private function pickRandomActivity($activities, $size)
    {
        $picked_events = $this->pickRandom($activities, $size);
        for ($i = 0; $i < sizeof($picked_events); $i++) {
            $picked_events[$i] = $this->parse($picked_events[$i]);
        }
        return $picked_events;

    }

    private function pickRandom($array, $size)
    {
        $reslut = array();
        foreach ($array as $value) {
            $reslut[] = $value;
        }
        for ($i = 0; $i < sizeof($array) - $size; $i++) {
            $index = $this->random($this->iday + 2, $i) % sizeof($reslut);
            array_splice($reslut, $index, 1);
        }
        return $reslut;

    }

    private function parse($event)
    {
        if (strpos($event['name'], "%v") !== false) {
            $index = $this->random($this->iday, 12) % sizeof($this->varNames);
            $event['name'] = str_replace("%v", $this->varNames[$index], $event['name']);
        }

        if (strpos($event['name'], "%t") !== false) {
            $index = $this->random($this->iday, 11) % sizeof($this->tools);
            $event['name'] = str_replace("%t", $this->tools[$index], $event['name']);
        }
        if (strpos($event['name'], "%l") !== false) {
            $event['name'] = str_replace("%l", (string) $this->random($this->iday, 12) % 247 + 30, $event['name']);
        }
        return $event;
    }
}
