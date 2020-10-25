<?php

/*
 * 评论表,暂时不使用，都使用第三方评论
 */

class Comment extends Eloquent {

    protected $table = 'comment';
    //fillable 属性指定哪些属性可以被集体赋值。这可以在类或接口层设置。
    //fillable 的反义词是 guarded，将做为一个黑名单而不是白名单：
    protected $fillable = array('title', 'machine_name', 'code_one', 'code_two', 'choose');
    //注意在默认情况下您将需要在表中定义 updated_at 和 created_at 字段。
    //如果您不希望这些列被自动维护，在模型中设置 $timestamps 属性为 false。
    public $timestamps = false;

    /**
     * 获取评论
     * @return html
     */
    public static function get($node) {
        $site_comment = Setting::find('site_comment');
        if ($site_comment['status']) {
            $comment = Comment::where('machine_name', '=', $site_comment['value'])->first();
            if ($comment['choose'] == 1) {
                $comment_code = $comment['code_one'];
            } else {
                $comment_code = $comment['code_two'];
            }
            if ($site_comment['value'] == 'duoshuo') {
                //多说数据组装
                $comment_code = str_replace("{{id}}", $node['id'], $comment_code);
                $comment_code = str_replace("{{title}}", $node['title'], $comment_code);
                $comment_code = str_replace("{{url}}", $node['url'], $comment_code);
                return $comment_code;
            }
            return $comment_code;
        }
        return false;
    }

}
