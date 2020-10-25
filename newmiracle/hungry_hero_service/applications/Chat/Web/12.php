<?php

   $host='127.0.0.1';

     $port=22301;

     $mem=new Memcache();

     $mem->connect($host,$port);

       $items=$mem->getExtendedStats ('items');

     $items=$items["$host:$port"]['items'];

    foreach($items as $key=>$values){

         $number=$key;;

       $str=$mem->getExtendedStats ("cachedump",$number,0);

      $line=$str["$host:$port"];

    if( is_array($line) && count($line)>0){

             foreach($line as $key=>$value){

                echo $key.'=>';

           print_r($mem->get($key));

             echo "\r\n";

           }

      }

    }

     ?>