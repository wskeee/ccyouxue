<?php

namespace common\models\games;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%game_login_qrcode}}".
 *
 * @property integer $id
 * @property integer $scene_id
 * @property integer $game_id
 * @property string $qrcodeurl
 * @property integer $expire_time
 * @property integer $created_at
 * @property integer $updated_at
 */
class GameLoginQrcode extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%game_login_qrcode}}';
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
            [['scene_id', 'game_id', 'expire_time', 'created_at', 'updated_at'], 'integer'],
            [['qrcodeurl'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('game', 'ID'),
            'scene_id' => Yii::t('game', 'Scene ID'),
            'game_id' => Yii::t('game', 'Game ID'),
            'qrcodeurl' => Yii::t('game', 'Qrcodeurl'),
            'expire_time' => Yii::t('game', 'Expire Time'),
            'created_at' => Yii::t('game', 'Created At'),
            'updated_at' => Yii::t('game', 'Updated At'),
        ];
    }
}
