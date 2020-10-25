# kephp

## 基本安装

```console
composer require kephp/kephp
composer require kephp/kephp ~version
composer require kephp/kephp ^version
composer update kephp/kephp
```

### composer 源切换

显示当前全局的源：

`composer config -g repo.packagist`

去掉当前设置的全局源：

`composer config -g --unset repos.packagist`

国内源：

`composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/`

国际源：

`composer config -g repo.packagist composer https://repo.packagist.org`

提示：

1. 尽量不要在项目的 composer.json 上配置源，会让项目难以适配更多的环境。
2. `https://packagist.phpcomposer.com` 这个源，更新 composer 有点慢，看情况使用。

### Windows

1. 下载最新的 [composer.phar](https://getcomposer.org/composer.phar)，到你希望安装的目录，比如 `d:\composer` 。
2. 用命令行进入这个目录，执行 `echo @php "%~dp0composer.phar" %* > composer.bat`，会在这个目录下产生一个 `composer.bat` 文件。
3. 将这个目录添加进系统变量的Path中。
4. 如果开发者在国内（国外请忽略），设置全局 composer 环境 `composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/`

### Linux

```console
wget https://getcomposer.org/composer.phar
php7 composer.phar install
```

