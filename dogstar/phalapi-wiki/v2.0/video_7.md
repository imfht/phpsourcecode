# 第七课 PhalApi 2.x 接口开发 - Domain领域业务层与ADM模式解说

[第七课 PhalApi 2.x 接口开发 - Domain领域业务层与ADM模式解说](https://www.bilibili.com/video/av88525328/)

[![http://cdn7.okayapi.com/yesyesapi_20200213150830_d1198acfe57f4fc94f882883b698120a.png](http://cdn7.okayapi.com/yesyesapi_20200213150830_d1198acfe57f4fc94f882883b698120a.png)](https://www.bilibili.com/video/av88525328/)

## 何为Api-Domain-Model模式？
是连接Api层与Model层的桥梁。

## 专注领域的Domain业务层
 - 最容易的一层（技术角度）
 - 也是最难的一层（业务角度）
 
## ADM职责划分
 - Api接口服务层应该做
 - Api接口服务层【不】应该做
 - Domain领域业务层应该做
 - Domain领域业务层【不】应该
 - Model数据模型层应该

## ADM调用关系
 - Api层调用Domain层
 - Domain层调用Domain层
 - Domain层调用Model层
 - Model层调用Model层

> 对于更复杂的项目，推荐使用ADSM模式，其中S表示service。
