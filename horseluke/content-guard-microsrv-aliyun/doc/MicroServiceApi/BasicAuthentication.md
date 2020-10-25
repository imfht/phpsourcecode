## API认证方式 - 使用HTTP普通鉴权(Basic Authentication)方式

### 请求方式

该方式即在请求header头部发送Authorization头，其组成方法为：

```
Authorization: Basic {base64编码串}
```

{base64编码串} = base64编码({用户名} + ':' + {密码})。

### 计算方式

其中：

{用户名}：unix timestamp，php使用time()获取即可。

{密码}：md5后的hash串，全大写。具体规则，为：

```
全大写(md5(
  {微服务appsecret}
   + 'm_appid=' + {微服务appid} 
   + '&m_ip=' + {用户访问ip} 
   +  '&m_time=' + {时间戳，即刚才的用户名}
))
```

### 小贴士

php中，若使用curl，只需要如下设置即可，无需手动自行组装该header头：

```
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);    //指定HTTP普通鉴权(Basic Authentication)方式
curl_setopt($ch, CURLOPT_USERPWD, {用户名} + ':' + {密码});    //无需base64编码和拼接Authorization头格式
```

