<?php
/**
 * https://gitee.com/litephp
 * http://www.dazhetu.cn/
 * jay_fun 410136330@qq.com
 * Date: 2019/1/8
 * Time: 16:01
 */

namespace LiteAdmin;


use think\Db;
use think\facade\Config;
use think\facade\Env;
use think\Loader;

class Node
{
    /**
     * 模块
     * @param $path
     * @return \Generator
     */
    private static function module($path) {
        // 所有后台项目都在admin模块下
        return ['admin'];

        /*$d = dir($path);
        while (false !== $dir = $d->read()){
            if ($dir === '.' || $dir === '..'){
                continue;
            }
            if (is_dir($path.DIRECTORY_SEPARATOR.$dir)){
                yield $dir;
            }
        }*/
    }

    /**
     * 控制器
     * @param $path
     * @return \Generator
     */
    private static function controller($path) {
        $d = dir($path);
        while (false !== $file = $d->read()){
            if ($file === '.' || $file === '..'){
                continue;
            }
            if (is_file($path.DIRECTORY_SEPARATOR.$file)){
                yield [
                    'namespace'=>str_replace('.php','',$file),
                    'controller_path'=>Loader::parseName(str_replace('.php','',$file),0),
                ];
            }else{
                foreach (self::controller($d->path.DIRECTORY_SEPARATOR.$file) as $controller){
                    yield [
                        'namespace' => Loader::parseName($file,0).'\\'.$controller['namespace'],
                        'controller_path' => Loader::parseName($file,0).'.'.$controller['controller_path']
                    ];
                }
            }
        }
    }

    /**
     * 刷新数据库节点数据
     * @return array
     * @throws \ReflectionException
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public static function reload() {
        $node = self::getFileNodes();
        foreach ($node as $key => $value){
            $id = Db::name('SystemAuthNode')->where('path',$value['path'])->value('id');
            if ($id){
                Db::name('SystemAuthNode')->where('path',$value['path'])->update($value);
            }else{
                Db::name('SystemAuthNode')->insert($value);
            }
        }

        $list = Db::name('SystemAuthNode')->column('id', 'path');
        $ids = [];
        foreach ($node as $item) {
            if (isset($list[$item['path']])) {
                $ids[] = $list[$item['path']];
            }
        }
        Db::name('SystemAuthNode')->whereNotIn('id', $ids)->delete();
    }

    /**
     * 扫描文件系统 获取全部节点
     * @throws \ReflectionException
     */
    public static function getFileNodes()
    {
        $app_path = Env::get('app_path');
        $node = [];
        foreach (self::module($app_path) as $module){
            $node[] = [
                'path'=>$module,
                'title'=>$module,
                'level'=>1
            ];
            foreach (self::controller($app_path.$module.DIRECTORY_SEPARATOR.'controller') as $controller){
                $class = Env::get('app_namespace').'\\'.$module.'\\'.'controller'.'\\'.$controller['namespace'];
                $instance = new \ReflectionClass($class);
                $node[] = [
                    'path' => "{$module}/{$controller['controller_path']}",
                    'title'=>self::getAnnotation('title',$instance->getDocComment())?:$controller['namespace'],
                    'level'=>2
                ];
                $parentMethods = [];
                if ($parent = $instance->getParentClass()){
                    foreach ($parent->getMethods() as $m){
                        $parentMethods[] = $m->getName();
                    }
                }
                $methods = $instance->getMethods();
                foreach ($methods as $method){
                    $method_name = $method->getName();
                    // 重写父类的方法和下划线开头的方法会忽略
                    if (in_array($method_name ,$parentMethods) || 0 === strpos($method_name,'_')){
                        continue;
                    }
                    $comment = $method->getDocComment();
                    $auth = self::getAnnotation('auth',$comment);
                    $method_name = strtolower($method_name);
                    $node[] = [
                        'path' => "{$module}/{$controller['controller_path']}/{$method_name}",
                        'title' => self::getAnnotation('title',$comment)?:$method_name,
                        'auth' => (false === $auth)?'2':$auth,
                        'level'=>3
                    ];
                }
            }
        }
        return $node;
    }

    /**
     * 从数据库中获取节点缓存
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getNodesData()
    {

        $list = Db::name('SystemAuthNode')->select();
        usort($list, function($x, $y) {
            return strcasecmp($x['path'],$y['path']);
        });
        return $list;
    }

    /**
     * 获取注解
     * @param $flag
     * @param $comment
     * @return bool
     */
    public static function getAnnotation($flag,$comment) {
        preg_match("/@{$flag}\s*([^\s]*)/i",$comment,$matches);
        return isset($matches[1])?$matches[1]:false;
    }
}