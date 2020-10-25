<?php
/**
 * Created by PhpStorm.
 * User: dust2
 * Date: 15-1-28
 * Time: 下午4:52
 */

use Illuminate\Database\Eloquent\Builder;

/**
 * 分享的标签模型（用于标识项目中的分享所属类别，一个分享可对应多个标签）
 * Class Tag
 */
class Tag extends Eloquent
{
    protected $table = 'tags';

    /**
     * 获取标签所属项目信息
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('Project', 'project_id', 'id');
    }

    /**
     * 获取含有当前标签的分享列表清单
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sharing()
    {
        return $this->belongsToMany('ProjectSharing', 'sharing_tag', 'tag_id', 'sharing_id');
    }

    /**
     * 获取标签列表清单
     * @param $projectId
     * @return array
     */
    public static function getTagsInfos($projectId)
    {
        $baseBuilder = self::getBaseBuilder($projectId);
        $baseTagsInfos = Paginate::paginateBuilder($baseBuilder)->toArray();
        return $baseTagsInfos;
    }

    /**
     * @param int $projectId
     * @return Illuminate\Database\Eloquent\Builder
     */
    protected static function getBaseBuilder($projectId)
    {
        return static::where('project_id', $projectId);
    }


    protected $guarded = ['id'];

}