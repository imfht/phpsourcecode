<?php

/*
 * 区块表
 */

class Block extends Eloquent {

    protected $table = 'block';
    //fillable 属性指定哪些属性可以被集体赋值。这可以在类或接口层设置。
    //fillable 的反义词是 guarded，将做为一个黑名单而不是白名单：
    protected $fillable = array('baid', 'machine_name', 'title', 'description', 'body', 'format', 'theme', 'status', 'weight', 'pages', 'cache');
    //注意在默认情况下您将需要在表中定义 updated_at 和 created_at 字段。
    //如果您不希望这些列被自动维护，在模型中设置 $timestamps 属性为 false。
    public $timestamps = false;

    /**
     * 用于前台读取区块内容
     * -----------------------------------------------------------
     */
    //获取单个区块内容
    public static function get($id) {
        $block = Self::find($id)->toArray();
        return View::make('system/block', array('block' => $block));
    }

    //获取整个区域下面的内容
    public static function get_info($name) {
        $area = Blockarea::where('machine_name', '=', $name)->get();
        foreach ($area as $row) {
            return $row->block;
        }
    }

}
