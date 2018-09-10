<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "t_vote_history".
 *
 * @property integer $id
 * @property integer $girl_id
 * @property integer $count
 * @property string $ip
 * @property integer $created_at
 *
 * @property Girls $girl
 */
class VoteHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 't_vote_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['girl_id', 'ip'], 'required'],
            [['girl_id', 'count', 'created_at'], 'integer'],
            [['ip'], 'string', 'max' => 16],
            [['girl_id'], 'exist', 'skipOnError' => true, 'targetClass' => Girls::className(), 'targetAttribute' => ['girl_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'girl_id' => 'Girl ID',
            'count' => 'Count',
            'ip' => 'Ip',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGirl()
    {
        return $this->hasOne(Girls::className(), ['id' => 'girl_id']);
    }
}
