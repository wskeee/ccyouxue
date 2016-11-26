<?php

namespace common\models;

use common\models\games\Scene;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%user_profile}}".
 *
 * @property integer $id            
 * @property string $u_id               用户id
 * @property string $country            国家
 * @property string $province           省份
 * @property string $city               城市
 * @property Scene $subscribe_scene     关注场景
 * @property string $subscribe_scene_id 关注场景id
 * @property string $language           语言 默认ch_CN
 * @property string $remark             备注
 * @property string $tagid_list         标签
 * @property integer $created_at
 * @property integer $updated_at
 */
class UserProfile extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_profile}}';
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
            [['u_id'], 'string', 'max' => 36],
            [['country', 'province', 'city', 'language', 'remark', 'tagid_list'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('ccyx', 'ID'),
            'u_id' => Yii::t('ccyx', 'U ID'),
            'country' => Yii::t('ccyx', 'Country'),
            'province' => Yii::t('ccyx', 'Province'),
            'city' => Yii::t('ccyx', 'City'),
            'subscribe_scene_id' => Yii::t('ccyx', 'Subscribe Scene Id'),
            'language' => Yii::t('ccyx', 'Language'),
            'remark' => Yii::t('ccyx', 'Remark'),
            'tagid_list' => Yii::t('ccyx', 'Tagid List'),
            'created_at' => Yii::t('ccyx', 'Created At'),
            'updated_at' => Yii::t('ccyx', 'Updated At'),
        ];
    }
    
    /**
     * 关注场景
     * @return ActiveQuery
     */
    public function getSubscribeScene(){
        return $this->hasOne(Scene::className(), ['id'=>'subscribe_scene_id']);
    }
}
