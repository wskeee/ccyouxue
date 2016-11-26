<?php

namespace common\models\games;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%scene}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $qrcodeurl
 * @property string $des
 * @property integer $created_at
 * @property integer $updated_at
 */
class Scene extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%scene}}';
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
            [['created_at', 'updated_at'], 'integer'],
            [['name', 'qrcodeurl', 'des'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('game', 'ID'),
            'name' => Yii::t('game', 'Name'),
            'qrcodeurl' => Yii::t('game', 'Qrcodeurl'),
            'des' => Yii::t('game', 'Des'),
            'created_at' => Yii::t('game', 'Created At'),
            'updated_at' => Yii::t('game', 'Updated At'),
        ];
    }
}
