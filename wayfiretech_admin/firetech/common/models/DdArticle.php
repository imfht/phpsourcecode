<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-07-13 08:42:35
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-29 01:52:06
 */

namespace common\models;

/**
 * This is the model class for table "dd_article".
 *
 * @property int    $id
 * @property int    $ishot
 * @property int    $pcate
 * @property int    $ccate
 * @property string $template
 * @property string $title
 * @property string $description
 * @property string $content
 * @property string $thumb
 * @property int    $incontent
 * @property string $source
 * @property string $author
 * @property int    $displayorder
 * @property string $linkurl
 * @property int    $createtime
 * @property int    $edittime
 * @property int    $click
 * @property string $type
 * @property string $credit
 */
class DdArticle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%article}}';
    }

    /**
     * 行为.
     */
    public function behaviors()
    {
        /*自动添加创建和修改时间*/
        return [
           [
               'class' => \common\behaviors\SaveBehavior::className(),
               'updatedAttribute' => 'createtime',
               'createdAttribute' => 'createtime',
           ],
        ];
    }

    public function getCate()
    {
        return $this->hasOne(DdArticleCategory::className(), ['id' => 'ccate']);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ishot', 'pcate', 'ccate', 'template', 'title', 'description', 'content', 'thumb', 'source', 'author', 'displayorder', 'linkurl'], 'required'],
            [['ishot', 'pcate', 'ccate', 'displayorder', 'createtime', 'edittime', 'click'], 'integer'],
            [['content', 'icon'], 'string'],
            [['template'], 'string', 'max' => 300],
            [['title', 'description'], 'string', 'max' => 200],
            [['thumb', 'source', 'credit'], 'string', 'max' => 255],
            [['author'], 'string', 'max' => 50],
            [['linkurl'], 'string', 'max' => 500],
            [['type'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ishot' => '是否热门',
            'pcate' => '一级分类',
            'ccate' => '二级分类',
            'template' => '文章模板',
            'title' => '标题',
            'description' => '描述',
            'content' => '内容',
            'thumb' => '图片',
            'source' => '引用',
            'author' => '作者',
            'displayorder' => '排序',
            'linkurl' => '链接地址',
            'createtime' => 'Createtime',
            'edittime' => 'Edittime',
            'click' => 'Click',
            'type' => '类型',
            'credit' => '点赞',
            'icon' => '图标',
        ];
    }
}
