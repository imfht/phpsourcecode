<?php

require_once('activerecord.php');
require_once('unit-test.php');

$db = DB::open('pgsql:host=127.0.0.1;dbname=test;', 'dba', '');

// Tables
$db->dropTable('zombies');
$db->dropTable('tweets');
$db->dropTable('comments');

$Zombie = $db->createTable('zombies',
                           'name varchar(64)'
);
$Tweet = $db->createTable('tweets',
                          'zombie_id integer',
                          'city_id integer',
                          'content varchar(128)'
);
$Comment = $db->createTable('comments',
                            'zombie_id integer',
                            'tweet_id integer',
                            'content varchar(128)'
);

$Zombie->hasMany('tweets')->by('zombie_id');
$Zombie->hasMany("received_comments")->by("tweet_id")->in("comments")->through("tweets");
$Zombie->hasAndBelongsToMany("commenters")->by("zombie_id")->in("zombies")->be("owner")->through("received_comments");

$Tweet->hasMany("comments")->by("tweet_id");

$ash = $Zombie->create("name:", "Ash");
$bob = $Zombie->create("name:", "Bob");
$jim = $Zombie->create("name:", "Jim");

$ashTweet = $ash->tweets->create("content:", "Hello from Ash");
$ashTweet->comments->create("zombie_id:", $bob->id, "content:", "Cool from Bob");
$ashTweet->comments->create("zombie_id:", $jim->id, "content:", "Cool from Jim");

foreach ($ash->commenters->all() as $commenter) {
    echo "{$commenter->id}: {$commenter->name}\n";
}
