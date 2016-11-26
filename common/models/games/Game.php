<?php

namespace common\models\games;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%ccyx_game}}".
 *
 * @property integer $id
 * @property integer $type
 * @property string $name
 * @property string $start_time
 * @property string $end_time
 * @property string $des
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Game extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%game}}';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'status', 'created_at', 'updated_at'], 'integer'],
            [['start_time', 'end_time'], 'safe'],
            [['name', 'des'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('game', 'ID'),
            'type' => Yii::t('game', 'Type'),
            'name' => Yii::t('game', 'Name'),
            'start_time' => Yii::t('game', 'Start Time'),
            'end_time' => Yii::t('game', 'End Time'),
            'des' => Yii::t('game', 'Des'),
            'status' => Yii::t('game', 'Status'),
            'created_at' => Yii::t('game', 'Created At'),
            'updated_at' => Yii::t('game', 'Updated At'),
        ];
    }
}
