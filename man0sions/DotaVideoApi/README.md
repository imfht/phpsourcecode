# DotaVideo 后台接口

##  一:视频信息
> 基于yii实现，全部采用正则匹配youku／soku网页，实现一下接口

前端app 
###  由react native实现 app 及源码下载  http://git.oschina.net/man0sions/DotaVideo
###  由angularjs 实现 源码下载  http://git.oschina.net/man0sions/angularVideos
### 截图

#####标题视频列表
![image](http://wx.wefi.com.cn/images/d1.jpg)

###  1：获取soku视频列表



```
http://101.200.130.104/DotaVideoApi/?r=Youku/GetSokuList&data={q:'dota',cp:1,od:1,lt:0,limitdate:0}
```


参数 | 说明|类型|默认
---|---|---|---
q | 搜索关键词|string|dota
cp | 当前页|int|1
od | 排序（1-综合拍讯,2-最新发布,3-最多播放）|int|1
lt | 时长1(0-10分钟),2(10-30分钟),3(30-60分钟),4(>60分钟) 0－不限|int|0
limitdate | 时间(7-本周，31-本月，365-本年，0-不限)|int|0

###### 返回值


```
{
    "status":{
        "errorcode":"OK",
        "errorinfo":""
    },
    "data":[
        {
            "name":"【DOTA情书】殿堂级卡尔,精彩刺激逆风局!",
            "pic":"http:\/\/g2.ykimg.com\/0542040856EB9AAD6A0A4F047481E20A",
            "duration":"52:39",
            "vid":"XMTUwMzcwMTg5Ng==",
            "user":"Dota情书",
            "userinfo":{
                "uname":"Dota情书",
                "upic":"http:\/\/g4.ykimg.com\/0130391F484F82790BC834065745D82FDF80F2-9C8B-5C1D-8CD2-E6E677EF6F70",
                "uid":"UNDI1NTMxMjMy"
            },
            "time":"19天前",
            "click_count":"540,192"
        },
       ....
    ]
}
```



### 2:获取自频道用户及视频列表

```
http://101.200.130.104/DotaVideoApi/?r=Youku/GetSokuUser&data={q:'dota',cp:1}
```
参数 | 说明|类型|默认
---|---|---|---
q | 搜索关键词|string|dota
cp | 当前页|int|1

###### 返回值


```
{
    "status":{
        "errorcode":"OK",
        "errorinfo":""
    },
    "data":[
        {
            "uname":"Dota情书",
            "upic":"http:\/\/g4.ykimg.com\/0130391F484F82790BC834065745D82FDF80F2-9C8B-5C1D-8CD2-E6E677EF6F70",
            "vlist":[
                {
                    "pic":"http:\/\/r3.ykimg.com\/0542010156FDE94C6A0A3F045B30B414",
                    "duration":"1:49:33",
                    "vid":"XMTUxODYwMTkyOA==",
                    "name":"【DOTA情书解说】两场激情逆风局,火女火枪二合一!",
                    "click_count":"839,981",
                    "time":"5天前"
                },
                {
                    "pic":"http:\/\/r2.ykimg.com\/0542010156F4C0DA6A0A41044DAC8150",
                    "duration":"1:18:38",
                    "vid":"XMTUxMTMwODA4MA==",
                    "name":"【情书DOTA解说】全能骑士和蝙蝠骑士",
                    "click_count":"802,992",
                    "time":"11天前"
                },
                {
                    "pic":"http:\/\/r2.ykimg.com\/0542040856EDD2406A0A460459995773",
                    "duration":"1:11:24",
                    "vid":"XMTUwNTU1NDIxNg==",
                    "name":"【DOTA情书】双SJ物力赏金,砍爆泉水!",
                    "click_count":"819,174",
                    "time":"17天前"
                }
            ],
            "uid":"UNDI1NTMxMjMy"
        },
      ...
    ]
}
```


### 3:取得自频道用户的信息


```
http://101.200.130.104/DotaVideoApi/?r=Youku/GetSokuAllUser&data={q:'dota',pg:1}
```

参数 | 说明|类型|默认
---|---|---|---
q | 搜索关键词|string|dota
pg | 取多少页 100［默认］|int|1

###### 返回值


```
{
    "status":{
        "errorcode":"OK",
        "errorinfo":""
    },
    "data":{
        "Dota情书":{
            "uname":"Dota情书",
            "upic":"http:\/\/g4.ykimg.com\/0130391F484F82790BC834065745D82FDF80F2-9C8B-5C1D-8CD2-E6E677EF6F70",
            "uid":"UNDI1NTMxMjMy"
        },
        "DotA_黑曼巴":{
            "uname":"DotA_黑曼巴",
            "upic":"http:\/\/g3.ykimg.com\/0130391F45555C7D824D1607E26B78096239A3-5CD0-A7C1-CCAD-333F3E3D4E0A",
            "uid":"UNTI5MTE2NjQw"
        },
       ....
    }
}
```


### 4:取得某用户所有的视频


```
http://101.200.130.104/DotaVideoApi/?r=Youku/GetYokuUserPlayList&data={uid:'UNDI1NTMxMjMy',cp:1,od:1}
```

参数 | 说明|类型|默认
---|---|---|---
uid | 用户id |string|UNDI1NTMxMjMy
cp | 当前页 1［默认］|int|1
od | 排序 （1-最新发布,2-最多播放,3-最多评论,4-最多收藏）|int|1

###### 返回值

```
{
    "status":{
        "errorcode":"OK",
        "errorinfo":""
    },
    "data":[
        {
            "name":"【DOTA情书解说】两场激情逆风局,火女火枪二合一!",
            "pic":"http:\/\/r4.ykimg.com\/0541010156FDE94C6A0A3F045B30B414",
            "duration":"1:49:33",
            "vid":"XMTUxODYwMTkyOA==",
            "user":"UNDI1NTMxMjMy",
            "time":"5天前",
            "click_count":"840,908"
        },
        ...
    ]
}
```

## 二：用户模块
> 基于yii ActiveRecord和sqlite，实现 用户注册、登录、订阅，取消等功能

