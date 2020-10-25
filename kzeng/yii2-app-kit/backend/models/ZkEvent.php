<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "zk_event".
 *
 * @property integer $event_id
 * @property string $title
 * @property string $desc
 * @property string $create_time
 */
class ZkEvent extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'zk_event';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'desc', 'create_time'], 'required'],
            [['create_time'], 'safe'],
            [['title'], 'string', 'max' => 64],
            [['desc'], 'string', 'max' => 256]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'event_id' => 'Event ID',
            'title' => 'Title',
            'desc' => 'Desc',
            'create_time' => 'Create Time',
        ];
    }
}


/*
--
-- 表的结构 `zk_event`
--

CREATE TABLE IF NOT EXISTS `zk_event` (
  `event_id` int(11) NOT NULL,
  `title` varchar(64) NOT NULL,
  `desc` varchar(256) NOT NULL,
  `create_time` datetime NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `zk_event`
--

INSERT INTO `zk_event` (`event_id`, `title`, `desc`, `create_time`) VALUES
(1, 'hello', 'this is hello', '2016-04-06 00:00:00'),
(2, 'hello', 'this is hello', '2016-04-06 00:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `zk_event`
--
ALTER TABLE `zk_event`
  ADD PRIMARY KEY (`event_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `zk_event`
--
ALTER TABLE `zk_event`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
*/