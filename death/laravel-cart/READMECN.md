# Laravel Cart

Laravel的购物车插件。

支持作用域、持久化、关联产品模型。 

适用于：购物车，愿望清单，购买清单，产品文章等收藏。


## 安装步骤：

加载composer包

    composer require tanwencn/laravel-cart
 
 如果您使用的是Laravel 5.5，那么这就是所有要做的事情。
 
 如果您仍然使用Laravel的5.4版本，那么最后一步是添加软件包的服务提供者并将其别名。 要做到这一点打开你的config/app.php文件。
 
 向providers数组添加一个新行：
 
    "Tanwencn\Cart\ServiceProvider::class"
    
    
配置数据库信息

在你的 .env 文件中配置以下信息，使Laravel与数据库链接保持正常状态:
```bash
DB_HOST=localhost
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret
```

发布并运行数据库迁移：
```
php artisan vendor:publish --tag=laravel-cart
php artisan migrate
```

安装完成。     

     
## 使用方法

购物车为您提供以下方法来使用：
        
    $product = Product::find(1);
    
    Cart::put($product, 2); //添加购物车
    
    Cart::update($item_key, 3); //个性购物车
    
    Cart::forget($item_key or [$item_key1, $item_key2]); //删除购物车
    
    Cart::forgetByModel($product or [$product1, $product2]); //删除购物车
    
    Cart::flush(); //清空购物车
          
    $items = Cart::all(); //获取购物车
    
    foreach($items as $item){
    
        $item->getItemKey(); //购物车项目的唯一标识
        
        $item->qty //数量
        
        $item->price //引用于 $product->price
        
        $item->cartable //返回添加的 $product 模型
        
        $item->subtotal //等于：$item->price * $item->qty
    
    }
    
    $items->subtotal(); //所有项目的subtotal总和

    
## 作用域
    
默认域:

    Cart::add($product); //等于:Cart::scope('default')->add($product);
    
    Cart::all(); //等于:Cart::scope('default')->all();
    
收藏列表：

    Cart::scope('wishlist')->add($product);
    
    Cart::scope('wishlist')->all();
    
购买清单：

    Cart::scope('order')->add($product);
    
    Cart::scope('order')->all();


## 持久化

默认情况下除了order作用域，其它作用域默认为在登陆的情况下保存数据到数据库，并在下次登陆时合并当前购物车。

若想取消作用域的持久化，可在config/cart.php配置:
        
    'order' => [
        'persistent' => false
    ]
