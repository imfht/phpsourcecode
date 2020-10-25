<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// | User: ShouKun Liu  |  Email:24147287@qq.com  | Time:2016/12/20 22:55
// +----------------------------------------------------------------------
// | TITLE: 路由
// +----------------------------------------------------------------------

namespace backend\models;


use backend\helps\Tree;
use yii\helpers\ArrayHelper;

class AdminRule extends BaseModel
{

    public static $firstMenu = ['0' => '顶级菜单'];

    public static function tableName()
    {
        return 'admin_rule';
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['pid', 'route', 'title', 'icon', 'type', 'status', 'condition', 'is_show', 'order', 'tips'];
        $scenarios[self::SCENARIO_UPDATE] = ['pid', 'route', 'title', 'icon', 'type', 'status', 'condition', 'is_show', 'order', 'tips'];
        return $scenarios;

    }

    /**
     * 获取菜单
     * @param string $id
     * @return array
     *
     */
    public static function getAllMenu($id = '')
    {
        $all = self::find()
            ->andWhere(['status'=>1])
            ->orderBy('order')->asArray()->all();
        if ($id) {
            $all = self::find()
                ->andWhere(['status'=>1])
                ->where(['<>', 'id', $id])->asArray()->all();
        }
        $dataList = array();
        if ($all) {
            //生成线性结构, 便于HTML输出
            $dataList = Tree::makeTreeForHtml($all);
            $dataList = array_map(function ($item) {
                $nbsp = '';
                for ($i = 1; $i <= $item['level']; $i++) {
                    $nbsp .= '─';//制表符
                }
                $nbsp .= '╊';//制表符
                $item['title'] = $nbsp . $item['title'];
                return $item;
            }, $dataList);
            $dataList = ArrayHelper::map($dataList, 'id', 'title');
        }
        $dataList = array_merge(self::$firstMenu, $dataList);
        return $dataList;
    }

    public function attributeValues()
    {
        return [
            'type' => [
                '1' => '权限和菜单',
                '2' => '权限',
                '3' => '菜单',
            ],
            'status' => [
                0 => '关闭',
                1 => '开启'
            ],
            'is_show' => [
                0 => '隐藏',
                1 => '显示'
            ]
        ];


    }

    public function attributeLabels()
    {

        return [
            'id' => '主键',
            'pid' => '上级ID',
            'route' => '路由',
            'title' => '名称',
            'icon' => '图标',
            'type' => '类型',
            'status' => '状态',
            'condition' => '描述',
            'is_show' => '显示',
            'order' => '排序',
            'tips' => '提示',

        ];


    }

    public function rules()
    {
        return [
            ['pid', 'default', 'value' => '0'],
            ['route', 'required', 'message' => '路由必须'],
            ['title', 'required', 'message' => '名称必须'],
            [['icon'], 'string', 'max' => 255],
            [['order'], 'string', 'max' => 11],
            [['tips'], 'string', 'max' => 255],
            ['type', 'required', 'message' => '类型必须'],
            ['status', 'required', 'message' => '状态必须'],
            ['is_show', 'required', 'message' => '显示必须'],
            ['condition', 'string', 'max' => 255],
        ];

    }

}