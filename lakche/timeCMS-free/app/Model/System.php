<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cache;

class System extends Model
{
  use SoftDeletes;

  protected $hidden = ['deleted_at', 'created_at'];

  public static function getValue(){
    $value = System::all();
    $system = [];
    foreach($value as $value){
      $system[$value['key']] = $value['value'];
    }
    return $system;
  }

  public static function saveValue($date = []){
    if(is_array($date)){
      foreach($date as $key => $value){
        $key = strip_tags($key);
        $option = System::where('key',$key)->first();
        if(!$option) $option = new System;
        $option->key = $key;
        $option->value = strip_tags($value);
        $option->save();
      }
      Cache::forget('system_info');
    }
  }

}
