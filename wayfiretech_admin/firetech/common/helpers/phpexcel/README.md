
```


导出数据(export):
-----
```
$data = [
    ['id' => 1, 'name' => '张三', 'sex' => '男', 'phone' => 15515181929, 'idc' => 211325659536289526],
    ['id' => 2, 'name' => '李四', 'sex' => '男'],
    ['id' => 5, 'name' => '王二', 'sex' => '女'],
    ['id' => 8, 'name' => '麻子', 'sex' => '女'],
];
ExportData::widget([
    'data' => $data,  // 必须
    'headers' => [],  // 第一行的标题栏
    'sheetTitle' => 'sheet',  // 工作表的标题
    'setFirstTitle' => false,  // 是否在第一行设置标题行
    'asAttachment' => false,  // 是否下载导出结果
    'fileName' => 'word.xls',  // 导出的 Excel 文件名
    'savePath' => 'uploads/excels/',  // 保存到服务器的路径
    'properties' => [],  // Excel 文件的属性列表
]);

// 在一个 Excel 中导出多个工作表:
$data = [
    'sheet1' => [
        ['id' => 1, 'name' => '张三', 'sex' => '男', 'phone' => 15515181929, 'idc' => 412728199201123271],
        ['id' => 2, 'name' => '李四', 'sex' => '男'],
        ['id' => 5, 'name' => '王二', 'sex' => '女'],
        ['id' => 8, 'name' => '麻子', 'sex' => '女'],
    ],
    'sheet2' => [
        ['id' => 11, 'name' => '张三', 'sex' => '男', 'phone' => 15515181929, 'idc' => 412728199201123271],
        ['id' => 12, 'name' => '李四', 'sex' => '男'],
        ['id' => 15, 'name' => '王二', 'sex' => '女'],
        ['id' => 18, 'name' => '麻子', 'sex' => '女'],
    ],
    'sheet3' => [
        ['id' => 21, 'name' => '张三', 'sex' => '男', 'phone' => 15515181929, 'idc' => 412728199201123271],
        ['id' => 22, 'name' => '李四', 'sex' => '男'],
        ['id' => 25, 'name' => '王二', 'sex' => '女'],
        ['id' => 28, 'name' => '麻子', 'sex' => '女'],
    ],
];
ExportData::widget([
    'data' => $data,  // 必须
    'isMultipleSheet' => true,  // 必须
    'sheetTitle' => ['sheet1' => 'sheet1', 'sheet2' => 'sheet2'],  // 工作表的标题
]);
```


导出数据模型(export):
-----
```
// 导出单个表, 并下载导出的Excel文件
ExportModel::widget([
    'models' => Post::find()->all(),  // 必须
    'asAttachment' => true,  // 默认值, 可忽略
]);

// 导出单个表, 并将文件保存到服务器, 返回导出后的 Excel 文件路径
$url = ExportModel::widget([
    'models' => Post::find()->all(),  // 必须
    'asAttachment' => false,  // false 时保存到服务器
    'fileName' => time() . '.xls',  // 默认为:'excel.xls'
]);
// return: $url = 'uploads/excel/1500597563.xls';

// 导出单个表中指定的列
ExportModel::widget([
    'models' => Post::find()->all(),  // 必须
    'columns' => ['id', 'real_name', 'file_name', 'file_size'],
    'headers' => ['id' => 'ID', 'real_name' => '源文件名', 'file_name' => '新文件路径', 'file_size' => '大小(B)'],
    // 'headers'数组中的键名必须是'columns'数组的值, 否则无效
]);

// 导出多个表, 一个 Excel 文件多个表
ExportModel::widget([
    'isMultipleSheet' => true,  // 导出多个表时, 必须为 true
    'models' => [
        'sheet1' => Post::find()->all(),
        'sheet2' => Article::find()->all(),
        'sheet3' => Effect::find()->all(),
    ],
    //指定导出的列
    'columns' => [
        'sheet1' => ['id', 'real_name', 'file_name', 'file_size'],
        'sheet2' => ['id', 'title', 'sort'],
        'sheet3' => ['id', 'title', 'summary', 'method', 'demo_url'],
    ],
    // 设置每个表的标题
    'headers' => [
        'sheet1' => ['id' => 'ID', 'real_name' => '源文件名', 'file_name' => '新文件路径', 'file_size' => '大小(B)'],
        'sheet2' => ['id' => 'ID', 'title' => '文章标题', 'sort' => '排序值'],
        'sheet3' => ['id' => 'ID', 'title' => '插件标题', 'summary' => '插件介绍', 'demo_url' => '演示地址'],
    ],
]);

// 更强的导出功能: 自定义导出数据的格式
ExportModel::widget([
    'models' => Post::find()->all(),  // 必须
    'columns' => [
        'id',
        'real_name',
        'file_name',
        [
            'attribute' => 'file_size',
            'header' => '文件大小',
            'format' => 'text',
            'value' => function($model){
                return Helper::byteFormat($model->file_size);  //eg: '363.38KB'
            }
        ],
        'created_at:datetime',  //eg: '2017年5月4日 上午7:41:25'
        [
            'attribute' => 'updated_at',
            'format' => 'date'  //eg: '2017年5月4日'
        ],
        [
            'attribute' => 'updated_at',
            'header' => '最后修改时间',
            'format' => ['date', 'php:Y-m-d'],  //eg: '2017-05-04'
        ]
    ],
    'headers' => ['id' => 'ID', 'real_name' => '源文件名', 'file_name' => '新文件路径'],
]);
```


导入(import):
-----
```
// 导入一个 Excel 文件
ImportFile::getData('uploads/excel/excel.xls', false);

// 导入一个多表 Excel 文件中指定的一个工作表
ImportFile::getData('uploads/excel/excel2.xls', false, true, 'sheet1');
ImportFile::getData('uploads/excel/excel2.xls', false, true, ['sheet2', 'sheet3']);
ImportFile::getData('uploads/excel/excel2.xls', false, false, [1, 2]);  // 索引从0开始
```
