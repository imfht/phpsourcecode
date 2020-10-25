API接口文档
===========

接口调用地址为：`[baseUrl]/api/?do=[do]`，所有请求使用POST方式提交，请求数据需要使用`RSA`公钥加密。

响应数据(`JSON`格式)在用户已授权登录的情况下是通过`AES`加密的；在未登录情况是不加密的。


## 接口配置

* 导入`db/token.sql`到数据库；
* 配置文件`root/config/base.php`添加`RSA`私钥配置：

```
// RSA私钥(内容或者文件名)
$config["rsaPrivateKey"] = '...';
```

`RSA`私钥(公钥)生成方法：

```
# 生成私钥
openssl genrsa -out api.key 2048
# 从私钥导出公钥
openssl rsa -in api.key -out api.pub -pubout
```


## 接口示例（PHP）

```
include_once 'root/common/helper.php';
include_once 'root/include/misc/RSA.php';
include_once 'root/include/misc/AES.php';

// 接口地址
define('API_URL', 'http://localhost/bookmark/api/?do=[do]');
// RSA公钥
define('RSA_PUBLIC_KEY', '...');

/**
 * 接口调用
 *
 * @param string $do 操作名称
 * @param array $params 请求参数
 * @param string $appSecret AES密钥
 *
 * @return array|null
 */
function apiCall($do, array $params, $appSecret = null)
{
    // 数据加密
    $rsa = new RSA();
    $rsa->setPublicKey(RSA_PUBLIC_KEY);
    $data = $rsa->encryptWithPublicKey(json_encode($params));
    // POST请求
    $url = str_replace('[do]', $do, API_URL);
    $res = http($url, $data);
    // 数据解密
    if (substr($res, 0, 1) != '{') {
        $aes = new AES();
        $res = $aes->decrypt($res, $appSecret);
    }
    // 响应数据
    return json_decode($res, true);
}
```


## 接口列表

### 用户授权

请求：

```
$do = 'User_Auth';
$params = array(
    'app_id' => 1,                  // 应用ID
    'app_secret' => '',             // 应用密钥(本地生成32位随机数)
    'oauth_type' => '',             // 第三方账号类型(weibo,qq,baidu)
    'oauth_token' => array(),       // 第三方登录后获取的授权信息(数组)
);
```

响应：

```
array(
    'status' => 1,                  // 状态(0:失败;1:成功)
    'msg' => '',                    // 提示信息
    'data' => array(                // 数据
        'user' => array(            // 用户信息
            'uid' => 1,             // 用户ID
            'name' => '',           // 用户昵称
            'avatar' => '',         // 用户头像图片URL
        ),
        'token' => '',              // 会话ID(其他操作需要提供此参数)
        'expire_time' => 0,         // 会话过期时间
        'create_time' => 0,         // 会话创建时间
    },
);
```

### 分类列表

请求：

```
$do = 'Category_GetList';
$params = array(
    'token' => '',                  // 会话ID
);
```

响应：

```
array(
    'status' => 1,                  // 状态(0:失败;1:成功)
    'msg' => '',                    // 提示信息
    'data' => array(                // 数据
        array(
            'id'=> 1,               // 分类ID
            'name' => '',           // 分类名称
            'sort' => 1,            // 分类排序值
            'is_default' => 0,      // 是否是默认分类(默认分类不能删除)
            'is_private' => 0,      // 是否为私有分类
            'ctime' => 0,           // 分类创建时间
        ),
        ...
    },
);
```

### 添加分类

请求：

```
$do = 'Category_Add';
$params = array(
    'token' => '',                  // 会话ID
    'name' => '',                   // 分类名称
    'is_private' => 0,              // 是否是私有分类
);
```

响应：

```
array(
    'status' => 1,                  // 状态(0:失败;1:成功)
    'msg' => '',                    // 提示信息
    'data' => 1,                    // 分类ID
);
```

### 修改分类

请求：

```
$do = 'Category_Edit';
$params = array(
    'token' => '',                  // 会话ID
    'id' => 1,                      // 分类ID
    'name' => '',                   // 分类名称
    'is_private' => 0,              // 是否是私有分类
);
```

响应：

```
array(
    'status' => 1,                  // 状态(0:失败;1:成功)
    'msg' => '',                    // 提示信息
    'data' => null,
);
```

### 删除分类

请求：

```
$do = 'Category_Edit';
$params = array(
    'token' => '',                  // 会话ID
    'id' => 1,                      // 分类ID
);
```

响应：

```
array(
    'status' => 1,                  // 状态(0:失败;1:成功)
    'msg' => '',                    // 提示信息
    'data' => null,
);
```

### 交换分类排序

请求：

```
$do = 'Category_Edit';
$params = array(
    'token' => '',                  // 会话ID
    'id1' => 1,                     // 分类ID1
    'id2' => 2,                     // 分类ID2
);
```

响应：

```
array(
    'status' => 1,                  // 状态(0:失败;1:成功)
    'msg' => '',                    // 提示信息
    'data' => null,
);
```

### 网址列表

请求：

```
$do = 'Link_GetList';
$params = array(
    'token' => '',                  // 会话ID
    'cid' => 1,                     // 分类ID(可选)
    'kw' => '',                     // 搜索关键词(可选)
    'page' => 1,                    // 页码
    'each' => 5,                    // 每页数量
);
```

响应：

```
array(
    'status' => 1,                  // 状态(0:失败;1:成功)
    'msg' => '',                    // 提示信息
    'data' => array(
        'list' => array(            // 网址列表
            array(
                'id' => 1,          // 网址ID
                'title' => '',      // 网址标题
                'url' => '',        // 网址URL
                'ctime' => 0,       // 网址添加时间
                'cid' => 0,         // 分类ID
            ),
            ...
        ),
        'count' => 0,               // 网址总量
        'page' => 1,                // 页码
        'each' => 5,                // 每页数量
    ),
);
```

### 添加网址

请求：

```
$do = 'Link_Add';
$params = array(
    'token' => '',                  // 会话ID
    'cid' => 0,                     // 分类ID
    'title' => '',                  // 网址标题
    'url' => '',                    // 网址URL
);
```

响应：

```
array(
    'status' => 1,                  // 状态(0:失败;1:成功)
    'msg' => '',                    // 提示信息
    'data' => 1,                    // 网址ID
);
```

### 修改网址

请求：

```
$do = 'Link_Edit';
$params = array(
    'token' => '',                  // 会话ID
    'id' => 1,                      // 网址ID
    'cid' => 0,                     // 分类ID
    'title' => '',                  // 网址标题
    'url' => '',                    // 网址URL
);
```

响应：

```
array(
    'status' => 1,                  // 状态(0:失败;1:成功)
    'msg' => '',                    // 提示信息
    'data' => null,
);
```

### 删除网址

```
$do = 'Link_Edit';
$params = array(
    'token' => '',                  // 会话ID
    'id' => 1,                      // 网址ID
);
```

响应：

```
array(
    'status' => 1,                  // 状态(0:失败;1:成功)
    'msg' => '',                    // 提示信息
    'data' => null,
);
```
