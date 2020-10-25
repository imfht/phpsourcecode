#模板语法
      Example: 模板语法 =>编译结果
      {$var} => <?php echo $var;?>
      
      常量：__STATIC__ => PHP常量STC_DIR目录的相对路径，方便外部CSS、JS等资源调用
      
      数组使用语法：
      $array = array('one'=>'hello','two'=>'world');
      {$array[0]} => <?php echo $array[0]?>
      {$array['one']} => <?php echo $array['one']?>
      
      模板循环语法：
       {from 0 to 100 ++}
       
       {/from}
      
      {loop($arr as $v)}
       value is {$v}
      {/loop}
      
      {loop($arr as $k=>$v)}
       key is {$k},value is {$v}
      {/loop}
      
      条件判断语法：
       {if($a<3)}
           do something
       {/if}
      
      {if($a<3)}
           do something
      {else}
           do else
      {/if}
      
      {if($a<3)}
           do something
      {elseif($a<10)}
           do elseif
      {else}
           do else
      {/if}
      
      {switch($a)}
      {case 'one'}
           do something
      {/case}
      {case 'two'}
           do something
      {/case}
      {default}
           do something
      {/switch}
      
            自带几个常用函数，如果不习惯，可以使用PHP原生语言为模板语言。