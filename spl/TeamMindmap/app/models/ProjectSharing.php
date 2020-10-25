<?php
/**
 * Created by PhpStorm.
 * User: dust2
 * Date: 15-1-28
 * Time: 下午4:43
 */

use Illuminate\Database\Eloquent\Builder;

/**
 * 项目中的分享模型
 * Class ProjectSharing
 */
class ProjectSharing extends Eloquent
{
    /**
     * 获取分享的资源，资源可多个
     */
    public function resource()
    {
        return $this->belongsToMany('Resource', 'sharing_resource', 'sharing_id', 'resource_id');
    }

    /**
     * 获取分享所属标签，标签可多个
     */
    public function tag()
    {
        return $this->belongsToMany('Tag', 'sharing_tag', 'sharing_id', 'tag_id');
    }

    /**
     * 获取分享所属项目信息
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('Project', 'project_id', 'id');
    }

    protected $table = 'sharings';

    /**
     * 获取分享的创建者信息
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creater()
    {
        return $this->belongsTo('User', 'creater_id', 'id');
    }

    /**
     * 获取分享基本信息
     * @param $projectId
     * @return array
     */
    public static function getBaseSharingsInfos($projectId)
    {
        $baseBuilder = self::getBaseBuilder($projectId);
        $baseSharingsInfo = Paginate::paginateBuilder($baseBuilder)->toArray();
        return $baseSharingsInfo;
    }

    /**
     * @param $projectId
     * @return Illuminate\Database\Eloquent\Builder
     */
    protected static function getBaseBuilder($projectId)
    {
        return ProjectSharing::where('project_id', $projectId)
            ->select(
                'sharings.id AS id',
                'sharings.name AS name',
                'sharings.content AS content',
                'sharings.project_id AS project_id',
                'sharings.created_at AS created_at',
                'sharings.updated_at AS updated_at'
            );
    }

    /**
     * @param Builder $builder
     * @param $tagId
     * @return Illuminate\Database\Eloquent\Builder
     */
    protected static function getBuilderByTag(Builder $builder, $tagId)
    {
        return $builder->leftJoin('sharing_tag', 'sharing_tag.tag_id', '=', $tagId);
    }

    /**
     * 根据标签获取分享基本信息
     * @param $projectId
     * @param $tagId
     * @return array
     */
    public static function getBaseSharingsInfosByTag($projectId, $tagId)
    {
        $baseBuilder = self::getBaseBuilder($projectId);
        $tagBuilder = self::getBuilderByTag($baseBuilder, $tagId);
        $baseSharingsInfo = Paginate::paginateBuilder($tagBuilder)->toArray();
        return $baseSharingsInfo;
    }

    protected $guarded = ['id'];

}