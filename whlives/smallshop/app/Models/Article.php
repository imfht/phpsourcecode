<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/8
 * Time: 下午5:11
 */

namespace App\Models;
use Illuminate\Support\Facades\DB;

/**
 * 文章
 * Class Article
 * @package App\Models
 */
class Article extends BaseModels
{
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_ON => '正常',
        self::STATUS_OFF => '锁定'
    ];

    protected $table = 'article';
    protected $guarded = ['id'];

    /**
     * 获取详情
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function content() {
        return $this->hasOne('App\Models\ArticleContent');
    }

    /**
     * 保存数据
     * @param int $id
     * @param  array $save_data 需要保存的数据
     * @return bool|mixed
     */
    public static function saveData($id = '', $save_data) {
        if (!$save_data) return false;
        try {
            $res = DB::transaction(function () use ($id, $save_data) {
                $content = $save_data['content'];
                unset($save_data['content']);
                if ($id) {
                    $res = self::where('id', $id)->update($save_data);
                    ArticleContent::where('article_id', $id)->update(['content' => $content]);
                } else {
                    $result = self::create($save_data);
                    $res = $result->id;
                    ArticleContent::create(['article_id' => $res, 'content' => $content]);
                }
                return $res;
            });
        } catch (\Exception $e) {
            $res = false;
        }
        return $res;
    }

}
