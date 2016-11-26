<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\web\UploadedFile;

/**
 * User model
 *
 * @property integer $id
 * @property string $type                   用户类型1微信关注用户2系统自建用户
 * @property string $unionid                公众号全局id
 * @property string $openid                 用户在公众号里的id
 * @property string $username               用户名
 * @property string $nickname               用户妮称
 * @property string $password_hash          用户密码
 * @property string $password_reset_token   
 * @property string $sex                    用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
 * @property string $email                  邮件
 * @property string $phone                  电话
 * @property string $headimgurl             头像
 * @property string $auth_key
 * @property integer $status
 * @property integer $subscribe             用户是否订阅该公众号标识，值为0时，代表此用户没有关注该公众号，拉取不到其余信息
 * @property integer $subscribe_time        用户关注时间，为时间戳。如果用户曾多次关注，则取最后关注时间
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 * @property UserProfile $profile           用户属性
 */
class User extends ActiveRecord implements IdentityInterface
{
    const USER_TYPE_WECHAT = 1;    //微信关注用户
    const USER_TYPE_SYSTEM = 2;    //系统自建用户
    
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;
    
    /** 创建场景 */
    const SCENARIO_CREATE = 'create';
    /** 更新场景 */
    const SCENARIO_UPDATE = 'update';
    /** 微信注册场景 */
    const SCENARIO_CREATE_WECHAT = 'create_wechat';
    /** 微信更新场景,只更新手机 */
    const SCENARIO_UPDATE_WECHAT = 'update_wechat';
    
    /** 性别 男 */
    const SEX_MALE = 1;
    /** 性别 女 */
    const SEX_WOMAN = 2;
    /**
     * 性别
     * @var array 
     */
    public static $sexName = [
        self::SEX_MALE => '男',
        self::SEX_WOMAN => '女',
    ];
    
    /* 重复密码验证 */
    public $password2;
    /* 微信头像 */
    public $headimgurl;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
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
    
    public function scenarios() 
    {
        return [
            self::SCENARIO_CREATE => 
                ['username','nickname','sex','email','password','password2','email','phone','avatar'],
            self::SCENARIO_UPDATE => 
                ['username','nickname','sex','email','password','password2','email','phone','avatar'],
            self::SCENARIO_CREATE_WECHAT => 
                ['unionid','openid','username','nickname','sex','email','email','phone','headimgurl','subscribe','subscribe_time','groupid','status'],
            self::SCENARIO_UPDATE_WECHAT => ['phone'],
            self::SCENARIO_DEFAULT => ['username','nickname']
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password','password2'],'required','on'=>[self::SCENARIO_CREATE]],
            [['username','nickname','email'],'required','on'=>[self::SCENARIO_CREATE,self::SCENARIO_UPDATE]],
            [['username'],'unique'],
            [['password'],'string', 'min'=>6, 'max'=>64],
            [['username'],'string', 'max'=>36, 'on'=>[self::SCENARIO_CREATE]],
            [['id','username','nickname', 'password', 'password_reset_token', 'email','avatar',], 'string', 'max' => 255],
            [['sex'], 'integer'],
            [['phone'],'required','on'=>[self::SCENARIO_UPDATE_WECHAT]],
            [['auth_key'], 'string', 'max' => 255],
            [['password_reset_token'], 'unique'],
            [['email'], 'email'],
            [['avatar'], 'image'],
            [['password2'],'compare','compareAttribute'=>'password'],
            [['avatar'], 'file', 'extensions' => 'jpg, png', 'mimeTypes' => 'image/jpeg, image/png']
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'nickname' => '昵称',
            'sex' => '性别',
            'auth_key' => '授权码',
            'password' => '密码',
            'password2'=>'确认密码',
            'password_reset_token' => '密码重置令牌',
            'email' => '邮箱',
            'ee' => 'EE',
            'phone' => '手机',
            'status' => '状态',
            'avatar' => '头像',
            'created_at' => '创建于',
            'updated_at' => '更新于',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * 设置密码
     * @param type $password
     */
    public function setPassword($password)
    {
        $this->password = strtoupper(md5($password));
    }
    
    /**
     * 密码验证
     * @param type $password    待验证密码
     * @return type boolean
     */
    public function validatePassword($password)
    {
        return strtoupper(md5($password)) == $this->password;
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
    
    /**
     * 用户属性
     * @return ActiveQuery
     */
    public function getProfile(){
        return $this->hasOne(UserProfile::className(), ['u_id'=>'id']);
    }
    
    /**
     * 
     * @param type $insert 
     */
    public function beforeSave($insert) 
    {
        if(parent::beforeSave($insert))
        {
            if(!$this->id)
                $this->id = md5(rand(1,10000) + time());    //自动生成用户ID
            $upload = UploadedFile::getInstance($this, 'avatar');
            if($upload != null)
            {
                $string = $upload->name;
                $array = explode('.',$string);
                //获取后缀名，默认为 jpg 
                $ext = count($array) == 0 ? 'jpg' : $array[count($array)-1];
                $uploadpath = $this->fileExists(Yii::getAlias('@filedata').'/avatars/');
                $upload->saveAs($uploadpath.$this->username.'.'.$ext);
                $this->avatar = '/filedata/avatars/'.$this->username.'.'.$ext.'?rand='.  rand(0, 1000);
            }
            
            
            if($this->scenario == self::SCENARIO_CREATE)
            {
                $this->setPassword($this->password);
            }else if($this->scenario ==  self::SCENARIO_UPDATE)
            {
                if(trim($this->password) == '')
                    $this->password = $this->getOldAttribute ('password');
                else
                    $this->setPassword ($this->password);
                
                if(trim($this->avatar) == '')
                    $this->avatar = $this->getOldAttribute ('avatar');
            }
            
            if($this->scenario == self::SCENARIO_CREATE)
                $this->generateAuthKey();
            
            if(trim($this->nickname) == '')
                $this->nickname = $this->username;
            
            return true;
        }else
            return false;
    }
    
    /**
     * 检查目标路径是否存在，不存即创建目标
     * @param string $uploadpath    目录路径
     * @return string
     */
    private function fileExists($uploadpath) {

        if (!file_exists($uploadpath)) {
            mkdir($uploadpath);
        }
        return $uploadpath;
    }
}
