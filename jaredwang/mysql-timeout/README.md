# mysql-timeout
Customizing the timeout for a database query

## Install
composer.json
```javascript
{
"require": {
    "wwaayyaa/mysql-timeout":  "dev-master"
  }
}
```
```$ composer install```
## Example

php code

```php
    $conf = Conf::$db1;
    print_r($conf);

    $d = new MysqlTimeout($conf);

    echo "now:".time()."\n";
    $r = $d->query('select sleep(2) as `sleep`,2 as `sec`;');
    print_r($r);
    echo "sleep 2s,now:".time()."\n";
    try{
    $r = $d->query('select sleep(5) as `sleep`,5 as `sec`;',3);
    }catch(Exception $e){
        echo sprintf("error message:%s ,error code : %d \n",$e->getMessage(),$e->getCode());
    }
    echo "sleep 5s but timeout is 3s,so throw a error.now:".time()."\n";

    $d = new MysqlTimeout($conf);//must new a MysqlTimeout. because 
    $r = $d->query('select sleep(5) as `sleep`,5 as `sec`;',6);
    print_r($r);
    echo "sleep 5s success,because timeout is 6s. now:".time()."\n";
    
    //Other methods
    $r = $d->update("update dtk_zhibo_chat_log set addtime=now() where id = 6 or id = 17;");
    echo sprintf("success rows:%d \n",$r);
    $r = $d->insert("insert into dtk_zhibo_chat_log (zhiboId,content) values (1292,'test');");
    echo sprintf("primary id:%d \n",$r);
```
result

```php
Array
(
    [host] => 127.0.0.1
    [port] => 3306
    [user] => root
    [password] => root
    [dbname] => test
    [charset] => utf8
)
now:1475056910
Array
(
    [0] => Array
        (
            [sleep] => 0
            [sec] => 2
        )

)
sleep 2s,now:1475056912
error message:timeout ,error code : 922922
sleep 5s but timeout is 3s,so throw a error.now:1475056915
Array
(
    [0] => Array
        (
            [sleep] => 0
            [sec] => 5
        )

)
sleep 5s success,because timeout is 6s. now:1475056920
success rows:2
primary id:1036

```

## Method

```php
	/**
     * @return mixed  query data
     */
	public function query($sql, $timeout = 3);

	/**
     * @return int   rows count
     */
	public function update($sql, $timeout = 3);

	/**
     * @return int   data primary id
     */
	public function insert($sql, $timeout = 3);

	/**
     * @return int   rows count
     */
	public function delete($sql, $timeout = 3);

```

## Args

### config
 - host | must '127.0.0.1'
 - port | non-must '3306'
 - user | must 'root'
 - password | must 'root'
 - dbname | must 'text'
 - charset | must 'utf8'
 - timeout | non-must 3

### timeout (defalut : ```3```)
 - The MsyqlTimeout inspection cycle is ```0.05``` seconds,
 - so the minimum value of the $timeout parameter is 0.05,
 - the maximum value depends on the PHP.ini ```max_execution_time``` and MySQL ```timeout settings```.

## Exception

### errorcode
>If the query timeout,  MysqlTimeout will throw an exception, the exception number ```922922```
