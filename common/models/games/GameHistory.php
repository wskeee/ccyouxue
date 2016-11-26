<?php

namespace common\models\games;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%ccyx_game_history}}".
 *
 * @property integer $id
 * @property string $u_id
 * @property integer $scene_id
 * @property integer $game_id
 * @property double $data
 * @property string $des
 * @property integer $created_at
 * @property integer $updated_at
 */
class GameHistory extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%game_history}}';
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
            [['u_id'], 'required'],
            [['scene_id', 'game_id', 'created_at', 'updated_at'], 'integer'],
            [['data'], 'number'],
            [['u_id'], 'string', 'max' => 36],
            [['des'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('game', 'ID'),
            'u_id' => Yii::t('game', 'U ID'),
            'scene_id' => Yii::t('game', 'Scene ID'),
            'game_id' => Yii::t('game', 'Game ID'),
            'data' => Yii::t('game', 'Data'),
            'des' => Yii::t('game', 'Des'),
            'created_at' => Yii::t('game', 'Created At'),
            'updated_at' => Yii::t('game', 'Updated At'),
        ];
    }
}
