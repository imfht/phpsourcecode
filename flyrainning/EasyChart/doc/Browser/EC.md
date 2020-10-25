# EC 对象

EC 是一个全局对象，用于管理页面所有EasyChart对象

## 属性

| 名称  | 默认值 |          说明          |
| ----- | ------ | ---------------------- |
| count | 0      | 包含对象数量           |
| item  | []     | EasyChart对象列表 数组 |


## 方法

|    名称    |  参数  |      返回值      |                             说明                             |
| ---------- | ------ | ---------------- | ------------------------------------------------------------ |
| addItem    | object | undefined        | 将已有EasyChart实例添加到EC                                  |
| add        | opt    | EasyChart Object | 使用opt配置创建一个新的对象 ，opt中必须包含HTML容器id        |
| getByID    | id     | EasyChart Object | 通过id获取EasyChart对象                                      |
| get        | id     | EasyChart Object | getByID的别名                                                |
| getByIndex | index  | EasyChart Object | 通过索引获取EasyChart对象                                    |
| resize     |        | undefined        | 调整大小，EC会自动绑定window的resize事件，一般情况下无需调用 |

> 有关opt参数设置，参考[EasyChart](EasyChart.md)的option章节
