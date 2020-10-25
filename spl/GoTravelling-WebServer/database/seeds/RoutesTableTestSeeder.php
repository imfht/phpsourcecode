<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 15-4-28
 * Time: 下午9:23
 */

use Illuminate\Database\Seeder;
use Helper\MongoHelper;
use App\Sight;

class RoutesTableTestSeeder extends Seeder
{
    public function run()
    {
        $creatorId = App\User::first()['_id'];

        DB::table('routes')->delete();

        $this->insertOnRecorder([
            'name' => '毕业游',
            'creator_id' => $creatorId,
            'status' => 'planning',
            'isPublic' => true,
            'description' => '就是个描述，随便写的.含广州',
            'lock' => false,
            'tag' => [
                'label' => 'entertaining',
                'name' => '畅玩'
            ],
            'created_at' => MongoHelper::buildMongoDate(null),
            'daily' => [
                [
                    '_id' => $this->getTestTime(),
                    'remark' => '出发与调整。从广州到海南（灰机）',
                    'date' => MongoHelper::buildMongoDate('2015-5-12'),
                    'sights' => [
                        [
                            'sights_id' => Sight::getSightId('美丽沙花园'),
                            'name' => '美丽沙花园',
                            'loc' =>  ['type'=>'Point', 'coordinates' => [20.067751, 110.325004]],
                        ],
                        [
                            'sights_id' => Sight::getSightId('星海湾豪生大酒店'),
                            'name' => '星海湾豪生大酒店',
                            'loc' =>  ['type'=>'Point', 'coordinates' => [20.075289, 110.36253]],
                        ],
                        [
                            'name' => '北京大学没有sightId',
                            'loc' => ['type'=>'Point', 'coordinates' => [40.056968, 116.307689]],
                        ]
                    ]
                ],
                [
                    '_id' => $this->getTestTime(),
                    'remark' => '第二天的行程',
                    'date' => MongoHelper::buildMongoDate('2015-5-12'),
                    'sights' => [
                        [
                            'sights_id' => Sight::getSightId('中央生態公園'),
                            'name' => '中央生態公園',
                            'loc' =>  ['type'=>'Point', 'coordinates' => [20.032351, 110.426332]],
                        ],
                        [
                            'sights_id' => Sight::getSightId('桂林洋旅遊區'),
                            'name' => '桂林洋旅遊區',
                            'loc' =>  ['type'=>'Point', 'coordinates' => [20.027664, 110.500253]],
                        ],
                        [
                            'sights_id' => Sight::getSightId('鴻洲埃德瑞皇家園林酒店'),
                            'name' => '鴻洲埃德瑞皇家園林酒店',
                            'loc' =>  ['type'=>'Point', 'coordinates' => [19.958076, 110.438506]],
                        ]
                    ]
                ]
            ],
            'transportation' => [
                [
                    '_id' => $this->getTestTime(),
                    'from_name' => '桂林洋旅遊區',
                    'from_sight_id' => Sight::getSightId('桂林洋旅遊區'),
                    'from_loc' => ['type'=>'Point', 'coordinates' => [19.958076, 110.438506]],
                    'to_name' => '鴻洲埃德瑞皇家園林酒店',
                    'to_sight_id' => Sight::getSightId('鴻洲埃德瑞皇家園林酒店'),
                    'to_loc' => ['type' => 'Point', 'coordinates' => [19.958076, 110.438506]],
                    'description' => [
                        'type' => 'drive',
                        'policy' => [
                            ['label' => '时间优先', 'name' => 'least_time']
                        ]
                    ],
                    'prize' => 200,
                    'consuming' => 30
                ],
                [
                    '_id' => $this->getTestTime(),
                    'from_name' => '美丽沙花园',
                    'from_sight_id' => Sight::getSightId('美丽沙花园'),
                    'from_loc' => ['type'=>'Point', 'coordinates' => [20.067751, 110.325004]],
                    'to_name' => '星海湾豪生大酒店',
                    'to_sight_id' => Sight::getSightId('星海湾豪生大酒店'),
                    'to_loc' => ['type'=>'Point', 'coordinates' => [20.075289, 110.36253]],
                    'description' => [
                        'type' => 'bus',
                        'policy' => [
                            ['label' => '时间优先', 'name' => 'least_time'],
                            ['label' => '不含地铁', 'name' => 'avoid_subway']
                        ]
                    ],
                    'prize' => 50,
                    'consuming' => 15
                ]
            ],
            'photo' => [
                [
                    '_id' => $this->getTestTime(),
                    'name' => 'spatra.jpg'
                ],
                [
                    '_id' => $this->getTestTime(),
                    'name' => 'default_head_image.png'
                ]
            ]
        ]);

        $this->insertOnRecorder([
            'name' => '广州一天随便游',
            'creator_id' => $creatorId,
            'status' => 'finished',
            'isPublic' => false,
            'description' => '就是个描述，随便写的',
            'lock' => false,
            'tag' => [],
            'created_at' => MongoHelper::buildMongoDate(null),
            'daily' => [
                [
                    '_id' => $this->getTestTime(),
                    'remark' => '随便游的行程',
                    'date' => MongoHelper::buildMongoDate('2015-4-23'),
                    'sights' => [
                        [
                            'sights_id' => Sight::getSightId('华南师范大学-西区学生宿舍3幢'),
                            'name' => '华南师范大学-西区学生宿舍3幢',
                            'loc' =>  ['type'=>'Point', 'coordinates' => [23.146137, 113.353112]],
                        ],
                        [
                            'sights_id' => Sight::getSightId('百脑汇科技大厦C座'),
                            'name' => '百脑汇科技大厦C座',
                            'loc' =>  ['type'=>'Point', 'coordinates' => [23.140022, 113.34639]],
                        ],
                        [
                            'sights_id' => Sight::getSightId('太古汇'),
                            'name' => '太古汇',
                            'loc' =>  ['type'=>'Point', 'coordinates' => [23.139931, 113.337713]],
                        ],
                        [
                            'sights_id' => Sight::getSightId('广州动物园'),
                            'name' => '广州动物园',
                            'loc' =>  ['type'=>'Point', 'coordinates' => [23.148754, 113.309761]],
                        ],
                        [
//                            'sights_id' => Sight::getSightId('欧亚酒店'),
                            'name' => '欧亚酒店',
                            'loc' =>  ['type'=>'Point', 'coordinates' => [23.151902, 113.312352]],
                        ]
                    ]
                ],
            ],
            'transportation' => [],
            'photo' => []
        ]);

        $this->insertOnRecorder([
            'name' => '北京游',
            'creator_id' => $creatorId,
            'status' => 'finished',
            'isPublic' => true,
            'description' => '就是个描述，随便写的',
            'lock' => false,
            'tag' => [
                'label' => 'eating',
                'name' => '美食'
            ],
            'created_at' => MongoHelper::buildMongoDate(null),
            'daily' => [
                [
                    '_id' => $this->getTestTime(),
                    'remark' => '行程1',
                    'date' => MongoHelper::buildMongoDate('2015-4-26'),
                    'sights' => [
                        [
                            'sights_id' => Sight::getSightId('百度大厦'),
                            'name' => '百度大厦',
                            'loc' =>  ['type'=>'Point', 'coordinates' => [40.056968, 116.307689]],
                        ],
                        [
                            'sights_id' => Sight::getSightId('圆明园遗址公园'),
                            'name' => '圆明园遗址公园',
                            'loc' => ['type'=>'Point', 'coordinates' => [40.01629, 116.314607]],
                        ],
                        [
                            'sights_id' => Sight::getSightId('北京大学'),
                            'name' => '北京大学',
                            'loc' => ['type'=>'Point', 'coordinates' => [39.997741, 116.316176]],
                        ],
                        [
                            'sights_id' => Sight::getSightId('北京世纪金源大饭店'),
                            'name' => '北京世纪金源大饭店',
                            'loc' => ['type'=>'Point', 'coordinates' => [39.959377, 116.287224]],
                        ],
                    ]
                ],
                [
                    '_id' => $this->getTestTime(),
                    'remark' => '行程2',
                    'date' => MongoHelper::buildMongoDate('2015-4-26'),
                    'sights' => [
                        [
                            'sights_id' => Sight::getSightId('卢沟桥文化旅游区'),
                            'name' => '卢沟桥文化旅游区',
                            'loc' => ['type'=>'Point', 'coordinates' => [39.85599, 116.231986]],
                        ],
                        [
                            'sights_id' => Sight::getSightId('北宫国家森林公园'),
                            'name' => '北宫国家森林公园',
                            'loc' => ['type'=>'Point', 'coordinates' => [39.868707, 116.127198]],
                        ],
                        [
                            'sights_id' => Sight::getSightId('源恒益生态酒店'),
                            'name' => '源恒益生态酒店',
                            'loc' => ['type'=>'Point', 'coordinates' => [39.852736, 116.174728]],
                        ]
                    ]
                ],
                [
                    '_id' => $this->getTestTime(),
                    'remark' => '行程2',
                    'date' => MongoHelper::buildMongoDate('2015-4-26'),
                    'sights' => [
                        [
                            'sights_id' => Sight::getSightId('天安门广场'),
                            'name' => '天安门广场',
                            'loc' => ['type'=>'Point', 'coordinates' => [39.912733, 116.404015]],
                        ],
                        [
                            'sights_id' => Sight::getSightId('午门'),
                            'name' => '午门',
                            'loc' => ['type'=>'Point', 'coordinates' => [39.920107, 116.403705]],
                        ],
                        [
                            'sights_id' => Sight::getSightId('御花园'),
                            'name' => '御花园',
                            'loc' => ['type'=>'Point', 'coordinates' => [39.927604, 116.403161]],
                        ]
                    ]
                ],

            ],
            'transportation' => [
                [
                    '_id' => $this->getTestTime(),
                    'from_name' => '天安门广场',
                    'from_sight_id' => Sight::getSightId('天安门广场'),
                    'from_loc' => ['type'=>'Point', 'coordinates' => [19.958076, 110.438506]],
                    'to_name' => '午门',
                    'to_sight_id' => Sight::getSightId('午门'),
                    'to_loc' => ['type'=>'Point', 'coordinates' => [19.958076, 110.438506]],
                    'description' => [
                        'type' => 'walk',
                        'policy' => []
                    ],
                    'prize' => 200,
                    'consuming' => 30
                ],
                [
                    '_id' => $this->getTestTime(),
                    'from_name' => '卢沟桥文化旅游区',
                    'from_sight_id' => Sight::getSightId('卢沟桥文化旅游区'),
                    'from_loc' => ['type'=>'Point', 'coordinates' => [20.067751, 110.325004]],
                    'to_name' => '北宫国家森林公园',
                    'to_sight_id' => Sight::getSightId('北宫国家森林公园'),
                    'to_loc' => ['type'=>'Point', 'coordinates' => [20.075289, 110.36253]],
                    'description' => [
                        'type' => 'drive',
                        'policy' => [
                            ['label' => '最短距离', 'name' => 'least_distance']
                        ]
                    ],
                    'prize' => 50,
                    'consuming' => 15
                ],
            ],
            'photo' => []
        ]);

        $this->insertOnRecorder([
            'name' => '随便游',
            'creator_id' => $creatorId,
            'status' => 'travelling',
            'isPublic' => false,
            'description' => '就是个描述，随便写的',
            'lock' => false,
            'tag' => [
                'label' => 'watching',
                'name' => '观光'
            ],
            'created_at' => MongoHelper::buildMongoDate(null),
            'daily' => [
                [
                    '_id' => $this->getTestTime(),
                    'remark' => '第1天的行程',
                    'date' => MongoHelper::buildMongoDate('2015-5-1'),
                    'sights' => [
                        [
                            'sights_id' => Sight::getSightId('腾讯大厦'),
                            'name' => '腾讯大厦',
                            'loc' => ['type'=>'Point', 'coordinates' => [22.546092, 113.941088]],
                        ],
                        [
                            'sights_id' => Sight::getSightId('月亮湾公园'),
                            'name' => '月亮湾公园',
                            'loc' => ['type'=>'Point', 'coordinates' => [22.507935, 113.905]],
                        ],
                        [
                            'sights_id' => Sight::getSightId('恒丰海悦国际酒店'),
                            'name' => '恒丰海悦国际酒店',
                            'loc' => ['type'=>'Point', 'coordinates' => [22.490802, 113.944802]],
                        ]
                    ]
                ],
                [
                    '_id' => $this->getTestTime(),
                    'remark' => '第2天的行程',
                    'date' => MongoHelper::buildMongoDate('2015-5-1'),
                    'sights' => [
                        [
                            'sights_id' => Sight::getSightId('岳麓山风景名胜区'),
                            'name' => '岳麓山风景名胜区',
                            'loc' => ['type'=>'Point', 'coordinates' => [22.587092, 113.88849]],
                        ],
                        [
                            'sights_id' => Sight::getSightId('湘府文化公园'),
                            'name' => '湘府文化公园',
                            'loc' => ['type'=>'Point', 'coordinates' => [28.194667, 112.943373]],
                        ],
                        [
                            'sights_id' => Sight::getSightId('湖南佳兴世尊酒店'),
                            'name' => '湖南佳兴世尊酒店',
                            'loc' => ['type'=>'Point', 'coordinates' => [28.218295, 112.939094]],
                        ]
                    ]
                ],
                [
                    '_id' => $this->getTestTime(),
                    'remark' => '第3天的行程',
                    'date' => MongoHelper::buildMongoDate('2015-5-1'),
                    'sights' => [
                        [
                            'sights_id' => Sight::getSightId('哈尔滨冰雪大世界'),
                            'name' => '哈尔滨冰雪大世界',
                            'loc' => ['type'=>'Point', 'coordinates' => [45.785779, 126.571317]],
                        ],
                        [
                            'sights_id' => Sight::getSightId('儿童公园'),
                            'name' => '儿童公园',
                            'loc' => ['type'=>'Point', 'coordinates' => [45.767474, 126.662979]],
                        ],
                        [
                            'sights_id' => Sight::getSightId('自由空间连锁宾馆大成店'),
                            'name' => '自由空间连锁宾馆大成店',
                            'loc' => ['type'=>'Point', 'coordinates' => [45.766688, 126.67072]],
                        ]
                    ]
                ],
                [
                    '_id' => $this->getTestTime(),
                    'remark' => '第4天的行程',
                    'date' => MongoHelper::buildMongoDate('2015-5-1'),
                    'sights' => [
                        [
                            'sights_id' => Sight::getSightId('乌鲁瓦提风景区'),
                            'name' => '乌鲁瓦提风景区',
                            'loc' => ['type'=>'Point', 'coordinates' => [36.851616, 79.451627]],
                        ],
                        [
                            'sights_id' => Sight::getSightId('尼雅遗址'),
                            'name' => '尼雅遗址',
                            'loc' => ['type'=>'Point', 'coordinates' => [37.069591, 82.69943]],
                        ]
                    ]
                ]
            ],
            'transportation' => [
                [
                    '_id' => $this->getTestTime(),
                    'from_name' => '儿童公园',
                    'from_sight_id' => Sight::getSightId('儿童公园'),
                    'from_loc' => ['type'=>'Point', 'coordinates' => [19.958076, 110.438506]],
                    'to_name' => '自由空间连锁宾馆大成店',
                    'to_sight_id' => Sight::getSightId('自由空间连锁宾馆大成店'),
                    'to_loc' => ['type'=>'Point', 'coordinates' => [19.958076, 110.438506]],
                    'description' => [
                        'type' => 'walk',
                        'policy' => []
                    ],
                    'prize' => 200,
                    'consuming' => 30
                ],
                [
                    '_id' => $this->getTestTime(),
                    'from_name' => '乌鲁瓦提风景区',
                    'from_sight_id' => Sight::getSightId('乌鲁瓦提风景区'),
                    'from_loc' => ['type'=>'Point', 'coordinates' => [20.067751, 110.325004]],
                    'to_name' => '尼雅遗址',
                    'to_sight_id' => Sight::getSightId('尼雅遗址'),
                    'to_loc' => ['type'=>'Point', 'coordinates' => [20.075289, 110.36253]],
                    'description' => [
                        'type' => 'bus',
                        'policy' => [
                            ['label' => '最少换乘', 'name' => 'least_exchange'],
                            ['label' => '最少步行距离', 'name' => 'least_walk']
                        ]
                    ],
                    'prize' => 50,
                    'consuming' => 15
                ],
            ],
            'photo' => []

        ]);

        $this->insertOnRecorder([
            'name' => '广州二日游',
            'creator_id' => \App\User::where('username', 'test')->first()->toArray()['_id'],
            'status' => 'planning',
            'isPublic' => true,
            'description' => '就是个描述，随便写的.含广州',
            'lock' => false,
            'tag' => [
                'label' => 'entertaining',
                'name' => '畅玩'
            ],
            'created_at' => MongoHelper::buildMongoDate(null),
            'daily' => [
                [
                    '_id' => $this->getTestTime(),
                    'remark' => '第一天的行程',
                    'date' => MongoHelper::buildMongoDate('2015-5-12'),
                    'sights' => [
                        [
                            'sights_id' => Sight::getSightId('长隆水上乐园-正门入口'),
                            'name' => '长隆水上乐园-正门入口',
                            'loc' =>  ['type'=>'Point', 'coordinates' => [23.005748, 113.330555]],
                        ],
                        [
                            'sights_id' => Sight::getSightId('中心湖公园'),
                            'name' => '中心湖公园',
                            'loc' =>  ['type'=>'Point', 'coordinates' => [23.056676, 113.402364]],
                        ]
                    ]
                ],
                [
                    '_id' => $this->getTestTime(),
                    'remark' => '第二天的行程',
                    'date' => MongoHelper::buildMongoDate('2015-5-12'),
                    'sights' => [
                        [
                            'sights_id' => Sight::getSightId('玛丽莲甜品第三金碧店'),
                            'name' => '玛丽莲甜品第三金碧店',
                            'loc' =>  ['type'=>'Point', 'coordinates' => [23.071389, 113.294706]],
                        ],
                        [
                            'sights_id' => Sight::getSightId('海珠湖北门'),
                            'name' => '海珠湖北门',
                            'loc' =>  ['type'=>'Point', 'coordinates' => [23.082943, 113.328659]],
                        ]
                    ]
                ]
            ],
            'transportation' => [
                [
                    '_id' => $this->getTestTime(),
                    'from_name' => '长隆水上乐园-正门入口',
                    'from_sight_id' => Sight::getSightId('长隆水上乐园-正门入口'),
                    'from_loc' => ['type'=>'Point', 'coordinates' => [23.005748, 113.330555]],
                    'to_name' => '中心湖公园',
                    'to_sight_id' => Sight::getSightId('中心湖公园'),
                    'to_loc' => ['type' => 'Point', 'coordinates' => [23.056676, 113.402364]],
                    'description' => [
                        'type' => 'bus',
                        'policy' => [
                            ['label' => '不含地铁', 'name' => 'avoid_subway']
                        ]
                    ],
                    'prize' => 200,
                    'consuming' => 30
                ]
            ],
            'photo' => [
                [
                    '_id' => $this->getTestTime(),
                    'name' => 'spatra.jpg'
                ],
                [
                    '_id' => $this->getTestTime(),
                    'name' => 'default_head_image.png'
                ]
            ]
        ]);

        $this->insertOnRecorder([
            'name' => '深圳一日游',
            'creator_id' => \App\User::where('username', 'test')->first()->toArray()['_id'],
            'status' => 'travelling',
            'isPublic' => true,
            'description' => '就是个描述，随便写的',
            'lock' => false,
            'tag' => [
                'label' => 'entertaining',
                'name' => '畅玩'
            ],
            'created_at' => MongoHelper::buildMongoDate(null),
            'daily' => [
                [
                    '_id' => $this->getTestTime(),
                    'remark' => '畅玩行程',
                    'date' => MongoHelper::buildMongoDate('2015-5-12'),
                    'sights' => [
                        [
                            'sights_id' => Sight::getSightId('欢乐谷-入口'),
                            'name' => '欢乐谷-入口',
                            'loc' =>  ['type'=>'Point', 'coordinates' => [22.545575, 113.985906]],
                        ],
                        [
                            'sights_id' => Sight::getSightId('正中时代广场'),
                            'name' => '正中时代广场',
                            'loc' =>  ['type'=>'Point', 'coordinates' => [23.056676, 113.402364]],
                        ],
                        [
                            'sights_id' => Sight::getSightId('南山公园海关登山口'),
                            'name' => '南山公园海关登山口',
                            'loc' =>  ['type'=>'Point', 'coordinates' => [22.503277, 113.924183]],
                        ]
                    ]
                ]
            ],
            'transportation' => [
                [
                    '_id' => $this->getTestTime(),
                    'from_name' => '欢乐谷-入口',
                    'from_sight_id' => Sight::getSightId('欢乐谷-入口'),
                    'from_loc' => ['type'=>'Point', 'coordinates' => [22.545575, 113.985906]],
                    'to_name' => '正中时代广场',
                    'to_sight_id' => Sight::getSightId('正中时代广场'),
                    'to_loc' => ['type' => 'Point', 'coordinates' => [23.056676, 113.402364]],
                    'description' => [
                        'type' => 'drive',
                        'policy' => [
                            ['label' => '时间优先', 'name' => 'least_time']
                        ]
                    ],
                    'prize' => 300,
                    'consuming' => 15
                ]
            ],
            'photo' => [
                [
                    '_id' => $this->getTestTime(),
                    'name' => 'spatra.jpg'
                ]
            ]
        ]);
    }

    private function getTestTime()
    {
        static $timeOffset = 0;

        $time = time() + $timeOffset;
        $timeOffset += 1;
        return $time;
    }

    private function insertOnRecorder($data)
    {
        $addId = DB::table('routes')->insertGetId($data);
        $addId = (string)$addId;

        foreach($data['daily'] as $currentDaily ){
            foreach($currentDaily['sights'] as $currentSight){
                if( isset($currentSight['sights_id']))
                    Sight::addRelativeRoute($currentSight['sights_id'], $addId);
            }
        }
    }

}