# LMLSQL
command line sql

## How to use?

### requirements

```
php5 pdo pdo_mysql
```

### create soft link

```
sudo ln -s /path/to/LMLSQL/lmlsql /usr/bin/lmlsql
```

### config identifier in dbconfig.php

```
<?php

return $dbconfig = array(

    'dev' => array(
        'hostname' => '192.168.169.12',
        'hostport' => '3306',
        'username' => 'username',
        'password' => 'password',
        'database' => 'database',
        'charset' => 'utf8',
        'persist' => false,
        'dbprefix' => '',
    ),

);
```

### query sql like this

```
lmlsql /dev "show create table db.tablename"

or

lmlsql dev "show create table db.tablename"
```
