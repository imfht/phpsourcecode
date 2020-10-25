<?php

namespace modules\doc\models;

/**
 * This is the ActiveQuery class for [[Doc]].
 *
 * @see Doc
 */
class DocQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Doc[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Doc|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
