# laravel-cart

Laravel Shopping cart.

Support scope, persistence, and relational product models. 

Applicable to: shopping cart, wish list, purchase list, product collection, posts and other collections.


## Installation Steps

Require the Package

    composer require tanwencn/laravel-cart
 
 If you're using Laravel 5.5, this is all there is to do.
 
 Should you still be on version 5.4 of Laravel, the final steps for you are to add the service provider of the package and alias the package. To do this open your config/app.php file.
 
 Add a new line to the providers array:

     providers 添加："Tanwencn\Cart\ServiceProvider::class"
     

Configuration database

Next make sure to create a new database and add your database credentials to your .env file:
```bash
DB_HOST=localhost
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret
```

Run
```php artisan vendor:publish --tag=laravel-cart```
And
```php artisan migrate```
     
The installation is complete.
   
  
## Usage

The shoppingcart gives you the following methods to use:
        
    $product = Product::find(1);
    
    Cart::put($product, 2); //Add Cart
    
    Cart::update($item_key, 3); //Update Cart
    
    Cart::forget($item_key or [$item_key1, $item_key2]); //Deletes Cart
    
    Cart::forgetByModel($product or [$product1, $product2]); //Deletes Cart
    
    Cart::flush(); //Flush Cart
          
    $items = Cart::all(); //Get Cart
    
    foreach($items as $item){
        
        $item->getItemKey(); //Shopping cart items are uniquely identified.
        
        $item->qty //quantity
        
        $item->price //Reference $product->price
        
        $item->cartable //return $product
        
        $item->subtotal //$item->price * $item->qty
        
    }
    
    $items->subtotal(); //all item subtotal

    
## Scope
    
default scope:

    Cart::add($product); //Equivalent to Cart::scope('default')->add($product);
    
    Cart::all(); //Equivalent to Cart::scope('default')->all();
    
wishlist：

    Cart::scope('wishlist')->add($product);
    
    Cart::scope('wishlist')->all();
    
Purchase list：

    Cart::scope('order')->add($product);
    
    Cart::scope('order')->all();

## Persistence

By default, other scopes (except for "order" scopes) default to saving data to the database at login and merge the current shopping cart at the next login.

To disable persistence of the scope, configure it in:

    'order' => [
    
        'persistent' => false
        
    ]