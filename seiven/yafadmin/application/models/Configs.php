<?php
class Configs extends ActiveRecord\Model {
    // explicit table name since our table is not "books"
    static $table_name = 'configs';
    
    // explicit pk since our pk is not "id"
    static $primary_key = 'id';
    
    // explicit connection name since we always want our test db with this model
    // static $connection = 'test';
    
    // explicit database name will generate sql like so => my_db.my_book
    // static $db = 'my_db';
}
