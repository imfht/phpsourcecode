<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 15-4-28
 * Time: 下午9:14
 */
use Illuminate\Database\Seeder;

class SightTableTestSeeder extends Seeder
{
    public function run()
    {
        DB::table('sights')->delete();

        DB::table('sights')->insert([
            'province' => '海南',
            'city' => '海口',
            'loc' => ['type'=>'Point', 'coordinates' => [20.067751, 110.325004]],
            'name' => '美丽沙花园',
            'description' => '呵呵哒',
            'address' => '海南省海口市美兰区海甸五西路78号',
            'images' => [],
            'check_in' => [],
            'check_in_num' => 0
        ]);

        DB::table('sights')->insert([
            'province' => '海南',
            'city' => '海口',
            'loc' => ['type'=>'Point', 'coordinates' => [20.032351, 110.426332]],
            'name' => '中央生態公園',
            'description' => '原生态',
            'address' => '海南省海口市美兰区',
            'images' => [],
            'check_in' => [],
            'check_in_num' => 0
        ]);

        DB::table('sights')->insert([
            'province' => '海南',
            'city' => '海口',
            'loc' => ['type'=>'Point', 'coordinates' => [20.027664, 110.500253]],
            'name' => '桂林洋旅遊區',
            'description' => '旅游区',
            'address' => '海南省海口市美兰区',
            'images' => [],
            'check_in' => [],
            'check_in_num' => 0
        ]);

        DB::table('sights')->insert([
            'province' => '海南',
            'city' => '海口',
            'loc' => ['type'=>'Point', 'coordinates' => [19.958076, 110.438506]],
            'name' => '鴻洲埃德瑞皇家園林酒店',
            'description' => '皇家酒店',
            'address' => '海口市美兰区海榆大道188号（南渡江畔）	',
            'images' => [],
            'check_in' => [],
            'check_in_num' => 0
        ]);

        DB::table('sights')->insert([
            'province' => '海南',
            'city' => '海口',
            'loc' => ['type'=>'Point', 'coordinates' => [20.075289, 110.36253]],
            'name' => '星海湾豪生大酒店',
            'description' => '大酒店',
            'address' => '美兰区新埠岛西苑路21号（南渡江入海口处）	',
            'images' => [],
            'check_in' => [],
            'check_in_num' => 0
        ]);

        DB::table('sights')->insert([
            'province' => '广东',
            'city' => '广州',
            'loc' => ['type'=>'Point', 'coordinates' => [23.146137, 113.353112]],
            'name' => '华南师范大学-西区学生宿舍3幢',
            'description' => '宿舍',
            'address' => '广州市天河区中山大道西55号华南师大校内生物山(地科院旁)	',
            'images' => [],
            'check_in' => [],
            'check_in_num' => 0
        ]);

        DB::table('sights')->insert([
            'province' => '广东',
            'city' => '广州',
            'loc' => ['type'=>'Point', 'coordinates' => [23.140022, 113.34639]],
            'name' => '百脑汇科技大厦C座',
            'description' => '电脑城',
            'address' => '天河区天河路590号(岗顶总统大酒店旁)',
            'images' => [],
            'check_in' => [],
            'check_in_num' => 0
        ]);

        DB::table('sights')->insert([
            'province' => '广东',
            'city' => '广州',
            'loc' => ['type'=>'Point', 'coordinates' => [23.139931, 113.337713]],
            'name' => '太古汇',
            'description' => '商城',
            'address' => '天河天河路383号(近地铁石牌桥站)',
            'images' => [],
            'check_in' => [],
            'check_in_num' => 0
        ]);

        DB::table('sights')->insert([
            'province' => '广东',
            'city' => '广州',
            'loc' => ['type'=>'Point', 'coordinates' => [23.148754, 113.309761]],
            'name' => '广州动物园',
            'description' => '动物园',
            'address' => '越秀区先烈中路120号(近动物园公交总站)',
            'images' => [],
            'check_in' => [],
            'check_in_num' => 0
        ]);

        DB::table('sights')->insert([
            'province' => '广东',
            'city' => '广州',
            'loc' => ['type'=>'Point', 'coordinates' => [23.151902, 113.312352]],
            'name' => '欧亚酒店',
            'description' => '动物园酒店',
            'address' => '广州市越秀区先烈东路186-188号(环市东路附近)',
            'images' => [],
            'check_in' => [],
            'check_in_num' => 0
        ]);

        DB::table('sights')->insert([
            'province' => '北京',
            'city' => '北京',
            'loc' => ['type'=>'Point', 'coordinates' => [40.056968, 116.307689]],
            'name' => '百度大厦',
            'description' => '度娘总部',
            'address' => '北京市海淀区上地十街10号',
            'images' => [],
            'check_in' => [],
            'check_in_num' => 0
        ]);

        DB::table('sights')->insert([
            'province' => '北京',
            'city' => '北京',
            'loc' => ['type'=>'Point', 'coordinates' => [40.01629, 116.314607]],
            'name' => '圆明园遗址公园',
            'description' => '圆明园哇塞',
            'address' => '北京市海淀区清华西路28号	',
            'images' => [],
            'check_in' => [],
            'check_in_num' => 0
        ]);
        DB::table('sights')->insert([
            'province' => '北京',
            'city' => '北京',
            'loc' => ['type'=>'Point', 'coordinates' => [40.056968, 116.307689]],
            'name' => '北京大学',
            'description' => '北京大学好塞里',
            'address' => '北京市海淀区颐和园路5号',
            'images' => [],
            'check_in' => [],
            'check_in_num' => 0
        ]);
        DB::table('sights')->insert([
            'province' => '北京',
            'city' => '北京',
            'loc' => ['type'=>'Point', 'coordinates' => [39.959377, 116.287224]],
            'name' => '北京世纪金源大饭店',
            'description' => '饭店来啦耶',
            'address' => '北京市北京海淀板井路69号',
            'images' => [],
            'check_in' => [],
            'check_in_num' => 0
        ]);
        DB::table('sights')->insert([
            'province' => '北京',
            'city' => '北京',
            'loc' => ['type'=>'Point', 'coordinates' => [39.85599, 116.231986]],
            'name' => '卢沟桥文化旅游区',
            'description' => '黄继光叶赛',
            'address' => '丰台区卢沟桥城南街77号',
            'images' => [],
            'check_in' => [],
            'check_in_num' => 0
        ]);
        DB::table('sights')->insert([
            'province' => '北京',
            'city' => '北京',
            'loc' => ['type'=>'Point', 'coordinates' => [39.868707, 116.127198]],
            'name' => '北宫国家森林公园',
            'description' => '森林公园耶',
            'address' => '丰台区长辛店镇大灰厂东路55号',
            'images' => [],
            'check_in' => [],
            'check_in_num' => 0
        ]);
        DB::table('sights')->insert([
            'province' => '北京',
            'city' => '北京',
            'loc' => ['type'=>'Point', 'coordinates' => [39.852736, 116.174728]],
            'name' => '源恒益生态酒店',
            'description' => '有住宿啦',
            'address' => '北京丰台区长辛店太子峪环岛西300米(长兴路9号)',
            'images' => [],
            'check_in' => [],
            'check_in_num' => 0
        ]);
        DB::table('sights')->insert([
            'province' => '北京',
            'city' => '北京',
            'loc' => ['type'=>'Point', 'coordinates' => [39.912733, 116.404015]],
            'name' => '天安门广场',
            'description' => '习大大',
            'address' => '北京市东城区东长安街',
            'images' => [],
            'check_in' => [],
            'check_in_num' => 0
        ]);
        DB::table('sights')->insert([
            'province' => '北京',
            'city' => '北京',
            'loc' => ['type'=>'Point', 'coordinates' => [39.920107, 116.403705]],
            'name' => '午门',
            'description' => '要砍头哇塞',
            'address' => '景山前街4号',
            'images' => [],
            'check_in' => [],
            'check_in_num' => 0
        ]);
        DB::table('sights')->insert([
            'province' => '北京',
            'city' => '北京',
            'loc' => ['type'=>'Point', 'coordinates' => [39.927604, 116.403161]],
            'name' => '御花园',
            'description' => '游花园啦嘿',
            'address' => '北京市东城区景山前街4号故宫博物院内',
            'images' => [],
            'check_in' => [],
            'check_in_num' => 0
        ]);

        DB::table('sights')->insert([
            'province' => '广东',
            'city' => '深圳',
            'loc' => ['type'=>'Point', 'coordinates' => [22.546092, 113.941088]],
            'name' => '腾讯大厦',
            'description' => '鹅厂',
            'address' => '深圳市南山区科技园科技中一路',
            'images' => [],
            'check_in' => [],
            'check_in_num' => 0
        ]);

        DB::table('sights')->insert([
            'province' => '广东',
            'city' => '深圳',
            'loc' => ['type'=>'Point', 'coordinates' => [22.507935, 113.905]],
            'name' => '月亮湾公园',
            'description' => '看月亮啦',
            'address' => '深圳市南山区前海路0333号阳光玫瑰园对面',
            'images' => [],
            'check_in' => [],
            'check_in_num' => 0
        ]);

        DB::table('sights')->insert([
            'province' => '广东',
            'city' => '深圳',
            'loc' => ['type'=>'Point', 'coordinates' => [22.490802, 113.944802]],
            'name' => '恒丰海悦国际酒店',
            'description' => '睡觉啦',
            'address' => '宝民二路127',
            'images' => [],
            'check_in' => [],
            'check_in_num' => 0
        ]);

        DB::table('sights')->insert([
            'province' => '湖南',
            'city' => '长沙',
            'loc' => ['type'=>'Point', 'coordinates' => [22.587092, 113.88849]],
            'name' => '岳麓山风景名胜区',
            'description' => '上山打老虎',
            'address' => '登高路58号',
            'images' => [],
            'check_in' => [],
            'check_in_num' => 0
        ]);

        DB::table('sights')->insert([
            'province' => '湖南',
            'city' => '长沙',
            'loc' => ['type'=>'Point', 'coordinates' => [28.194667, 112.943373]],
            'name' => '湘府文化公园',
            'description' => '去公园耍',
            'address' => '天心区杉木冲西路9号湖南省政府',
            'images' => [],
            'check_in' => [],
            'check_in_num' => 0
        ]);

        DB::table('sights')->insert([
            'province' => '湖南',
            'city' => '长沙',
            'loc' => ['type'=>'Point', 'coordinates' => [28.218295, 112.939094]],
            'name' => '湖南佳兴世尊酒店',
            'description' => '睡个好觉',
            'address' => '长沙市金星中路与咸嘉湖交汇处',
            'images' => [],
            'check_in' => [],
            'check_in_num' => 0
        ]);

        DB::table('sights')->insert([
            'province' => '黑龙江',
            'city' => '哈尔滨',
            'loc' => ['type'=>'Point', 'coordinates' => [45.785779, 126.571317]],
            'name' => '哈尔滨冰雪大世界',
            'description' => '好冷耶',
            'address' => '松北区西侧冰雪大世界园区内(冰雪大世界,香格里拉酒店)	',
            'images' => [],
            'check_in' => [],
            'check_in_num' => 0
        ]);

        DB::table('sights')->insert([
            'province' => '黑龙江',
            'city' => '哈尔滨',
            'loc' => ['type'=>'Point', 'coordinates' => [45.767474, 126.662979]],
            'name' => '儿童公园',
            'description' => '好多儿童',
            'address' => '南岗区果戈里大街295号(近花园街)',
            'images' => [],
            'check_in' => [],
            'check_in_num' => 0
        ]);

        DB::table('sights')->insert([
            'province' => '黑龙江',
            'city' => '哈尔滨',
            'loc' => ['type'=>'Point', 'coordinates' => [45.766688, 126.67072]],
            'name' => '自由空间连锁宾馆大成店',
            'description' => '困了',
            'address' => '哈尔滨市南岗区中和街20号（与大成街交口）',
            'images' => [],
            'check_in' => [],
            'check_in_num' => 0
        ]);

        DB::table('sights')->insert([
            'province' => '新疆',
            'city' => '和田',
            'loc' => ['type'=>'Point', 'coordinates' => [36.851616, 79.451627]],
            'name' => '乌鲁瓦提风景区',
            'description' => '哇擦',
            'address' => '和田县625县道	',
            'images' => [],
            'check_in' => [],
            'check_in_num' => 0
        ]);

        DB::table('sights')->insert([
            'province' => '新疆',
            'city' => '和田',
            'loc' => ['type'=>'Point', 'coordinates' => [37.069591, 82.69943]],
            'name' => '尼雅遗址',
            'description' => '哇擦',
            'address' => '和田市买迪尼也提路8号',
            'images' => [],
            'check_in' => [],
            'check_in_num' => 0
        ]);

        DB::table('sights')->insert([
           'province' => '广东',
           'city' => '广州',
           'loc' => ['type' => 'Point', 'coordinates' => [ 23.005748, 113.330555 ]],
           'name' => '长隆水上乐园-正门入口',
           'description' => '游泳啦',
           'address' => '广州市番禺区迎宾路长隆旅游度假区长隆水上乐园内',
           'images' => [],
           'check_in' => [],
           'check_in_num' => 0
        ]);

// 测试特填数据－－－－－

        DB::table('sights')->insert([
           'province' => '广东',
           'city' => '广州',
           'loc' => ['type' => 'Point', 'coordinates' => [ 23.056676, 113.402364 ]],
           'name' => '中心湖公园',
           'description' => '大学城的公园',
           'address' => '广州市番禺区南三路与环湖路交汇处',
           'images' => [],
           'check_in' => [],
           'check_in_num' => 0
        ]);

        DB::table('sights')->insert([
           'province' => '广东',
           'city' => '广州',
           'loc' => ['type' => 'Point', 'coordinates' => [ 23.071389, 113.294706 ]],
           'name' => '玛丽莲甜品第三金碧店',
           'description' => '吃甜品啦',
           'address' => '第三金碧花园74/79幢030铺',
           'images' => [],
           'check_in' => [],
           'check_in_num' => 0
        ]);

        DB::table('sights')->insert([
           'province' => '广东',
           'city' => '广州',
           'loc' => ['type' => 'Point', 'coordinates' => [ 23.082943, 113.328659 ]],
           'name' => '海珠湖北门',
           'description' => '看湖水',
           'address' => '海珠区大塘地铁站b口附近',
           'images' => [],
           'check_in' => [],
           'check_in_num' => 0
        ]);

        DB::table('sights')->insert([
           'province' => '广东',
           'city' => '广州',
           'loc' => ['type' => 'Point', 'coordinates' => [ 23.083633, 113.313638 ]],
           'name' => '上涌果树公园-北门',
           'description' => '砍。。。果树',
           'address' => '广东省广州市海珠区新滘西路',
           'images' => [],
           'check_in' => [],
           'check_in_num' => 0
        ]);

        DB::table('sights')->insert([
           'province' => '广东',
           'city' => '深圳',
           'loc' => ['type' => 'Point', 'coordinates' => [ 22.732269, 114.257727 ]],
           'name' => '正中时代广场',
           'description' => '走在时代的广场',
           'address' => '区龙岗中心城9区龙城大道与龙福大道交汇处',
           'images' => [],
           'check_in' => [],
           'check_in_num' => 0
        ]);

        DB::table('sights')->insert([
           'province' => '广东',
           'city' => '深圳',
           'loc' => ['type' => 'Point', 'coordinates' => [ 22.545575, 113.985906 ]],
           'name' => '欢乐谷-入口',
           'description' => '欢乐地玩',
           'address' => '深圳市南山区深圳欢乐谷内',
           'images' => [],
           'check_in' => [],
           'check_in_num' => 0
        ]);

        DB::table('sights')->insert([
           'province' => '广东',
           'city' => '深圳',
           'loc' => ['type' => 'Point', 'coordinates' => [ 22.503277, 113.924183 ]],
           'name' => '南山公园海关登山口',
           'description' => '去登山哇',
           'address' => '南山区沿山路8号',
           'images' => [],
           'check_in' => [],
           'check_in_num' => 0
        ]);
    }

}