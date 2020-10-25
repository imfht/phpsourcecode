
## Input  输入过滤

输入参数过滤器，支持GET、POST、REQUEST、SERVER、ENV、COOKIE

注意$_SERVER中只有少量元素出现在INPUT_SERVER中

```php
$input = \Cute\Web\Input::getInstance('GET'); //或者$app->input('GET');
$params = $input->all();  //所有GET参数
$query_string = http_build_query($params); //安全的网址后参数
$page = $input->get('page', 1, 'int'); //获取页码，整数类型
//等同于$_REQUEST['page']，不过REQUEST只能单个获取，没有all()方法
$page = \Cute\Web\Input::request('page', 1, 'int');
```
