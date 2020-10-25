<?php
class Hook {

  static private $tags = array();

  /**
   * 动态添加插件到某个标签
   * @param string $tag 标签名称
   * @param mixed $name 插件名称
   * @return void
   */
  static public function add($tag, $name) {
    if (!isset(static::$tags[$tag])) {
      static::$tags[$tag] = array();
    }
    if (is_array($name)) {
      static::$tags[$tag] = array_merge(static::$tags[$tag], $name);
    } else {
      static::$tags[$tag][] = $name;
    }
  }

  /**
   * 批量导入插件
   * @param array $data 插件信息
   * @param boolean $recursive 是否递归合并
   * @return void
   */
  static public function import($data, $recursive=true) {
    if (!$recursive) { // 覆盖导入
      static::$tags = array_merge(static::$tags, $data);
    } else { // 合并导入
      foreach ($data as $tag=>$val){
        if (!isset(static::$tags[$tag])) {
          static::$tags[$tag] = array();
        }
        if (!empty($val['_overlay'])) {
          static::$tags[$tag] = $val;
        } else {
          // 合并模式
          static::$tags[$tag] = array_merge(static::$tags[$tag], $val);
        }
      }
    }
  }

  /**
   * 获取插件信息
   * @param string $tag 插件位置 留空获取全部
   * @return array
   */
  static public function get($tag='') {
    if (empty($tag)) {
      return static::$tags;
    } else {
      return static::$tags[$tag];
    }
  }

  /**
   * 监听标签的插件
   * @param string $tag 标签名称
   * @param mixed $params 传入参数
   * @return void
   */
  static public function listen($tag, &$params=NULL) {
    if (isset(static::$tags[$tag])) {
      foreach (static::$tags[$tag] as $name) {
        $result = static::exec($name, $tag, $params);
        if (false === $result) {
          // 如果返回false 则中断插件执行
          return ;
        }
      }
    }
    return;
  }

  /**
   * 执行某个插件
   * @param string $name 插件名称
   * @param string $tag 方法名（标签名）
   * @param Mixed $params 传入的参数
   * @return void
   */
  static public function exec($name, $tag, &$params=NULL) {
    if (is_file(ADDON_PATH . "{$name}/config.php")) {
      include ADDON_PATH . "{$name}/config.php";
    }
    include ADDON_PATH . "{$name}/cls.{$name}.php";
    $addon = new $name();
    return $addon->$tag($params);
  }
}
