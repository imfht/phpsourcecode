<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Cron]].
 *
 * @see Cron
 */
class CronQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Cron[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Cron|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
