<?php
/**
 * Created by PhpStorm.
 * User: yidashi
 * Date: 16/7/13
 * Time: 下午9:04
 */

namespace common\modules\comment\widgets;

use common\assets\LayerAsset;
use yii\base\InvalidParamException;
use yii\base\Widget;
use yii\data\ActiveDataProvider;
use common\modules\comment\models\Comment;

class CommentWidget extends Widget
{
    public $entity = 'document';

    public $entityId;
    /**
     * @var \yii\db\ActiveRecord
     */
    public $model;
    public $listTitle = '评论';
    public $createTitle = '发表评论';

    public function init()
    {
        parent::init();
        if (isset($this->model)) {
            $this->entity = $this->model->getEntity();
            $this->entityId = $this->model->getEntityId();
        } else if ($this->entity && $this->entityId) {
            $entityClassName = $this->entity;
            $this->model = $entityClassName::findOne($this->entityId);
        } else {
            throw new InvalidParamException('缺少model或者entity&&entityId');
        }
    }
    public function run()
    {
        if (!isset($this->model) || $this->model->getCommentEnabled()) {
            // 评论列表
            $dataProvider = new ActiveDataProvider([
                'query' => Comment::find()->andWhere(['entity' => $this->entity, 'entity_id' => $this->entityId, 'status' => 1, 'parent_id' => 0]),
                'pagination' => [
                    'pageSize' => 10,
                ],
                'sort' => [
                    'defaultOrder' => [
                        'is_top' => SORT_DESC,
                        'id' => SORT_ASC
                    ]
                ]
            ]);
            if (isset($this->model)) {
                $commentTotal = $this->model->getCommentTotal();
            } else {
                $commentTotal = Comment::activeCount($this->entity, $this->entityId);
            }
            // 评论框
            $commentModel = new Comment();
            $commentModel->entity = $this->entity;
            $commentModel->entity_id = $this->entityId;
            LayerAsset::register($this->view);
            return $this->render('index', [
                'entity' => $this->entity,
                'entityId' => $this->entityId,
                'commentTotal' => $commentTotal,
                'commentModel' => $commentModel,
                'dataProvider' => $dataProvider,
                'listTitle' => $this->listTitle,
                'createTitle' => $this->createTitle,
            ]);
        }
        return '';
    }
}