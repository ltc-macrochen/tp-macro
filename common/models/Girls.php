<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "t_girls".
 *
 * @property integer $id
 * @property string $name
 * @property string $head
 * @property integer $vote_count
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property VoteHistory[] $tVoteHistories
 */
class Girls extends \yii\db\ActiveRecord
{
    // 用户状态
    const GIRLS_STATUS_NORMAL   = 0; // 正常
    const GIRLS_STATUS_DISABLE  = 1; // 禁用

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 't_girls';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'head'], 'required'],
            [['vote_count', 'status', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 32],
            [['head'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '姓名',
            'head' => '头像',
            'vote_count' => '票数',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTVoteHistories()
    {
        return $this->hasMany(VoteHistory::className(), ['girl_id' => 'id']);
    }

    /**
     * 所有状态
     * @return array
     */
    public static function getAllGirlsStatus()
    {
        return [
            static::GIRLS_STATUS_NORMAL => '正常',
            static::GIRLS_STATUS_DISABLE => '删除'
        ];
    }

    /**
     * 状态名
     * @return mixed|string
     */
    public function getStatusName()
    {
        $allStatus = static::getAllGirlsStatus();
        return isset($allStatus[$this->status]) ? $allStatus[$this->status] : '-';
    }
}
