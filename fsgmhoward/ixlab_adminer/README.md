# IXLab_Adminer
用于萌网（MoeNet Inc）贴吧云签到的Adminer数据库管理器

## 特色
* 体积小巧（跟PhpMyAdmin之类的大型数据库管理器相比）
* 自动读取云签到的数据库设置，实现管理员一键登录数据库
* 更多介绍请看[我的博客](https://blog.ixnet.work/2016/01/22/adminer/)。

## 安装方法
* 从Release页面或网盘下载已经打包好的ZIP包，直接上传安装
* 或者使用以下指令自行打包成ZIP档案（处于output文件夹内），上传云签进行安装
```
$ git clone https://gitee.com/fsgmhoward/ixlab_adminer.git
$ php compile.php mysql zh
```

## 更多信息
* 请看[我的博客](https://blog.ixnet.work/2016/01/22/adminer/)。
* 想用百度网盘下载的也可以进入我的博文，里面有网盘地址。

## 更新内容
请看Release界面。

## 关于BUG反馈
* 如果有与云签相关的问题（无法自动登录数据库等）麻煩直接提交Issue。
* 如果是其它的问题，请到[GitHub原Repo处](https://github.com/vrana/adminer)提交Issue。

## 开源协议（Apache License 2.0）
Copyright 2016 Howard Liu

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.