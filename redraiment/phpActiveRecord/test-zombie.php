<?php

require_once('activerecord.php');
require_once('unit-test.php');

$db = DB::open('pgsql:host=127.0.0.1;dbname=zombie;', 'dba', '');

// Tables
$db->dropTable('zombies');
$db->dropTable('cities');
$db->dropTable('tweets');
$db->dropTable('comments');
$db->dropTable('relations');

$Zombie = $db->createTable('zombies',
                           'name varchar(64)'
);
$City = $db->createTable('cities',
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
$Relation = $db->createTable('relations',
                             'following integer',
                             'follower integer'
);

// Relations
$Zombie->hasMany('tweets')->by('zombie_id');
$Zombie->hasAndBelongsToMany('travelled_cities')->by('city_id')->in('cities')->through('tweets');
$Zombie->hasMany("received_comments")->by("tweet_id")->in("comments")->through("tweets");
$Zombie->hasMany("send_comments")->by("zombie_id")->in("comments");
$Zombie->hasMany("follower_relations")->by("following")->in("relations")->be("a");
$Zombie->hasAndBelongsToMany("followers")->by("follower")->in("zombies")->be("followers")->through("follower_relations");
$Zombie->hasMany("following_relations")->by("follower")->in("relations");
$Zombie->hasAndBelongsToMany("followings")->by("following")->in("zombies")->be("followings")->through("following_relations");

$City->hasMany("tweets")->by("city_id");
$City->hasAndBelongsToMany("zombies")->by("zombie_id")->through("tweets");

$Tweet->belongsTo("zombie")->in("zombies");
$Tweet->belongsTo("city")->in("cities");
$Tweet->hasMany("comments")->by("tweet_id");

$Comment->belongsTo("zombie")->by("zombie_id")->in("zombies");
$Comment->belongsTo("tweet")->by("tweet_id")->in("tweets");

// Meta Data
$boston = $City->create("name:", "Boston");
$newyork = $City->create("name:", "NewYork");

$ash = $Zombie->create("name:", "Ash");
$ashTweets = $ash->tweets;
$ashTweetOnBoston = $ashTweets->create("city_id:", $boston->id, "content:", "Hello Boston from Ash!")->comments;
$ashTweetOnNewYork = $ashTweets->create("city_id:", $newyork->id, "content:", "Hello NewYork from Ash!")->comments;

$bob = $Zombie->create("name:", "Bob");
$bobTweets = $bob->tweets;
$bobTweetOnBoston = $bobTweets->create("city_id:", $boston->id, "content:", "Hello Boston from Bob!")->comments;
$bobTweetOnNewYork = $bobTweets->create("city_id:", $newyork->id, "content:", "Hello NewYork from Bob!")->comments;

$jim = $Zombie->create("name:", "Jim");
$jimTweets = $jim->tweets;
$jimTweetOnBoston = $jimTweets->create("city_id:", $boston->id, "content:", "Hello Boston from Jim!")->comments;
$jimTweetOnNewYork = $jimTweets->create("city_id:", $newyork->id, "content:", "Hello NewYork from Jim!")->comments;

$ashTweetOnBoston->create("zombie_id:", $bob->id, "content:", "Cool from Bob @ Boston");
$ashTweetOnBoston->create("zombie_id:", $jim->id, "content:", "Cool from Jim @ Boston");
$ashTweetOnNewYork->create("zombie_id:", $bob->id, "content:", "Cool from Bob @ NewYork");
$ashTweetOnNewYork->create("zombie_id:", $jim->id, "content:", "Cool from Jim @ NewYork");
$bobTweetOnBoston->create("zombie_id:", $ash->id, "content:", "Cool from Ash @ Boston");
$bobTweetOnBoston->create("zombie_id:", $jim->id, "content:", "Cool from Jim @ Boston");
$bobTweetOnNewYork->create("zombie_id:", $ash->id, "content:", "Cool from Ash @ NewYork");
$bobTweetOnNewYork->create("zombie_id:", $jim->id, "content:", "Cool from Jim @ NewYork");
$jimTweetOnBoston->create("zombie_id:", $ash->id, "content:", "Cool from Ash @ Boston");
$jimTweetOnBoston->create("zombie_id:", $bob->id, "content:", "Cool from Bob @ Boston");
$jimTweetOnNewYork->create("zombie_id:", $ash->id, "content:", "Cool from Ash @ NewYork");
$jimTweetOnNewYork->create("zombie_id:", $bob->id, "content:", "Cool from Bob @ NewYork");

$Relation->create("following:", $ash->id, "follower:", $bob->id);
$Relation->create("following:", $ash->id, "follower:", $jim->id);
$Relation->create("following:", $bob->id, "follower:", $ash->id);
$Relation->create("following:", $bob->id, "follower:", $jim->id);
$Relation->create("following:", $jim->id, "follower:", $ash->id);
$Relation->create("following:", $jim->id, "follower:", $bob->id);

// Validate Create

assertEquals(5, count($db->getTableNames()));

$cityNames = ['Boston', 'NewYork'];
$cities = $City->all();
assertEquals(count($cityNames), count($cities));
for ($i = 0; $i < count($cityNames); $i++) {
    assertEquals($cityNames[$i], $cities[$i]->name);
}

$zombieNames = ['Ash', 'Bob', 'Jim'];
$zombies = $Zombie->all();
assertEquals(count($zombieNames), count($zombies));
for ($i = 0; $i < count($zombieNames); $i++) {
    assertEquals($zombieNames[$i], $zombies[$i]->name);
}

$tweets = $Tweet->all();
assertEquals(count($zombieNames) * count($cityNames), count($tweets));
$i = 0;
foreach ($zombieNames as $zombie) {
    foreach ($cityNames as $city) {
        assertEquals("Hello {$city} from {$zombie}!", $tweets[$i]->content);
        $i++;
    }
}

$comments = $Comment->all();
assertEquals((count($zombieNames) - 1) * count($zombieNames) * count($cityNames), count($comments));
$i = 0;
foreach ($zombieNames as $zombie) {
    foreach ($cityNames as $city) {
        foreach ($zombieNames as $friend) {
            if ($zombie !== $friend) {
                assertEquals("Cool from {$friend} @ {$city}", $comments[$i]->content);
                $i++;
            }
        }
    }
}

$relations = $Relation->all();
$i = 0;
foreach ($zombies as $zombie) {
    foreach ($zombies as $friend) {
        if ($zombie->id !== $friend->id) {
            $relation = $relations[$i];
            $i++;

            assertEquals($zombie->id, $relation->following);
            assertEquals($friend->id, $relation->follower);
        }
    }
}

// Validate Relation
foreach ($Zombie->all() as $zombie) {
    $relations = [
        'tweets' => 2,
        'travelled_cities' => 2,
        'received_comments' => 4,
        'send_comments' => 4,
        'follower_relations' => 2,
        'followers' => 2,
        'following_relations' => 2,
        'followings' => 2
    ];
    foreach ($relations as $relation => $count) {
        assertEquals($count, count($zombie->$relation->all()));
    }
}

foreach ($City->all() as $city) {
    assertEquals(3, count($city->tweets->all()));
    assertEquals(3, count($city->zombies->all()));
}

foreach ($Tweet->all() as $tweet) {
    assertNotNull($tweet->zombie);
    assertNotNull($tweet->city);
    assertEquals(2, count($tweet->comments->all()));
}

foreach ($Comment->all() as $comment) {
    assertNotNull($comment->zombie);
    assertNotNull($comment->tweet);
}

// Validate Query
assertEquals(1, $Comment->first()->id);
assertEquals(12, $Comment->last()->id);
$list = $Comment->paging(3, 4);
assertEquals(4, count($list));
for ($i = 0; $i < 4; $i++) {
    assertEquals(9 + $i, $list[$i]->id);
}
