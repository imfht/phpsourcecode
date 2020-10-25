<?php declare (strict_types = 1);

/**
 * 解析foreach
 * @example
 *  $arr = ['arr'=>['cache'=>true, 'value'=>[1, 2, 3, 4, 5, 6, 7, 8, 9, 0]]];
 *  <{foreach $arr as $k => $v}>
 *      <{$k}>
 *  <{endforeach}>
 *  ---->
 *  <?php foreach($arr as $k=>$v) : ?>
 *      <?php echo $k?>
 *  <?php endforeach;?>
 *  ---->  0123456789;
 * @example
 *  $arr = [
 *      'arr'=>[
 *          'cache'=>true,
 *          'value'=>[
 *              'a'=>['A','B','C'],
 *              'b'=>['D','E','F']
 *          ]
 *      ]
 *  ];
 *  <{foreach $arr as $key => $value}>
 *      <{$key}>:
 *      <{foreach $value as $v}>
 *          <{$v}>
 *      <{endforeach}>
 *  <{endforeach}>;
 *  ---->
 *  <?php foreach($arr as $key=>$value) : ?>
 *      <?php echo $key;?>
 *      <?php foreach($value as $v)?>
 *          <?php echo $v;?>
 *      <?php endforeach;?>
 *  <?php endforeach;?>
 *  ---->  a:ABCb:DEF
 */

/**
 * 解析变量
 * @example         <{name}>      -->  <?php echo $name;?>
 * @example (cache) <{name}>      -->  value
 */

/**
 * 数组解析
 * @example $array = ['name'=>'liming', age=13];
 * @example <{array.name}>       ---->   <?php echo $array['name'];?>  -----> liming
 * @example <{array.age}>        ---->   <?php echo $array['age'];?>   -----> 13
 */

/**
 * 解析函数
 * 规则:
 *     1.有参数,参数缓存,直接替换
 *     2.有参数,参数部分缓存,缓存参数替换为值
 *     3.无参数,不缓存
 * @example
 *     $a = 'test';
 *     $c = 5;
 *     原始标签           ----> 无缓存效果                  ----> 缓存效果(仅$a缓存)
 *     <{time()}>         ----> <?php echo time();?>        ----> <?php echo (string) time();?>
 *     <{substr(a, 2)}>  ----> <?php echo substr($a,2);?>  ----> 'st'
 *     <{substr(a, c)}> ----> <?php echo substr($a,$c);?> ----> <?php echo (string) substr('test',$c);?>
 */

/**
 * 解析语言
 * @example <{language.username}>  =>    用户名 | username (一次解析, 直接替换)
 * @example <{lang.username}>      =>    用户名 | username (一次解析, 直接替换)
 */

/**
 * 解析常量
 * @example <{constant.IMAGE}>  =>    http:// image.test.com/ (一次解析, 直接替换)
 * @example <{cont.IMAGE}>      =>     http:// image.test.com/ (一次解析, 直接替换)
 */
