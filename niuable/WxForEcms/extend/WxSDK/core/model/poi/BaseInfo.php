<?php
namespace WxSDK\core\model\poi;

class BaseInfo
{
    public $sid;
    public $poi_id;//修改时
    public $business_name;//15个汉字或30个英文字符内",
    public $branch_name;//不超过10个字，不能含有括号和特殊字符",
    public $province;//不超过10个字",
    public $city;//"不超过30个字",
    public $district;//"不超过10个字",
    public $address;//"门店所在的详细街道地址（不要填写省市信息）：不超过80个字",
    public $telephone;//"不超53个字符（不可以出现文字）",
    public $categories;//["美食,小吃快餐"],
    public $offset_type;//1,
    public $longitude;//115.32375,
    public $latitude;//25.097486,
    public $photo_list;//[{"photo_url":"https:// 不超过20张.com"}，{"photo_url":"https://XXX.com"}],
    public $recommend;//"不超过200字。麦辣鸡腿堡套餐，麦乐鸡，全家桶",
    public $special;//"不超过200字。免费wifi，外卖服务",
    public $introduction;//"不超过300字。麦当劳是全球大型跨国连锁餐厅，1940 年创立于美国，在世界上大约拥有3 万间分店。
    public $open_time;//"8:00-20:00",
    public $avg_price;//35
    
    
}

