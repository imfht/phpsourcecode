# PhalApi 2.x 接口开发 - Api接口层

[视频：第六课 PhalApi 2.x 接口开发 - Api接口层](https://www.bilibili.com/video/av88288690/)

[![](http://cd7.yesapi.net/AABB2C92F6305FE82FCD493AD9226FC3_20200212103212_87695208c59a5227bbfb37faf6e59aa6.png)](https://www.bilibili.com/video/av88288690/)

## 说在前面的话
 + (经典)传统的MVC模式架构
   - Model模型层
   - View视图层
   - Controller控制层
 + （当前）PhalApi的ADM模式架构
   - Api接口控制层
   - Domain领域业务层
   - Model数据模型层
 + （扩展）FecMall开源商城（基于Yii2）
   - 模块 --> controller --> block --> services --> model --> view
   
## Api层职责是什么？
主要职责是(3步：接收参数、决策调度、返回结果)：
 + 处理并接收客户端传递的参数（通过配置接口参数可实现大部分逻辑）
 + 进行高层决策，逻辑控制和对底层的调度
 + 将处理结果返回给客户端

## 一个简单的示例

## 接口返回结果
 - 第1块：数据内容（数据）
 - 第2块：字段结构（ret/data/msg）
 - 第3块：序列化格式（json/xml/jsonp/serialiaze）
 
## 接口返回
 + 正常返回
 + 失败返回
 + 返回其他状态码
   - 方式一：通过抛出异常返回ret状态码
   - 方式二：手动指定ret状态码

## 扩展：修改默认返回的ret/data/msg结构

```json
{
  "error_status": 200,
  "error_message": "",
  "result": {
    "title": "Hello PhalApi",
    "version": "2.10.1",
    "time": 1581426712
  }
}
```

## 扩展：返回JSONP、XML等其他格式

