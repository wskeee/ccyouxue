<?php

namespace frontend\controllers;

use common\models\User;
use common\wskeee\taobao\TaobaoSMS;
use maxwen\easywechat\Wechat;
use Yii;
use yii\web\NotFoundHttpException;

class UserController extends WCAuthController
{
    public function actionIndex()
    {
        return $this->render('index');
    }
    
    /**
     * 个人中心
     */
    public function actionProfile(){
        /* @var $wechat Wechat */
        $wechat = Yii::$app->wechat;
        // 获取 OAuth 授权结果用户信息
        
        if($wechat->isWechat && !$wechat->isAuthorized()) {
            return $wechat->authorizeRequired()->send();
        }
        $user = User::findOne(['openid'=>$wechat->user->openId]);
        return $this->render('profile',['user'=>$user]);
    }    
    
    /**
     * 更新个人中心数据
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(User::SCENARIO_UPDATE_WECHAT);
        if($model->load(Yii::$app->getRequest()->post()))
        {
            if($model->save())
                return $this->redirect(['profile']);
            else
                Yii::error ($model->errors);
        }else
        {
            return $this->render('update',['model'=>$model]);
        }
    }
    
    /**
     * 检查手机是否存在
     */
    public function actionCheckMobileExists(){
        Yii::$app->getResponse()->format = 'json';
        $post = Yii::$app->getRequest()->getQueryParams();
        $post = array_merge($post, Yii::$app->getRequest()->getBodyParams());
        
        $mobile = $post['mobile'];
        $count = User::find()->where(['phone'=>$mobile])->count();
        return [
            'code'=>0,
            'msg'=>'',
            'data'=>[
                'mobile'=>$mobile,
                'isExist'=>$count>0,
            ]
        ];
    }
    
    /**
     * 发送验证码
     */
    public function actionSendVcode(){
        Yii::$app->getResponse()->format = 'json';
        $post = Yii::$app->getRequest()->getQueryParams();
        $post = array_merge($post, Yii::$app->getRequest()->getBodyParams());
        $code = rand(100000, 999999);
        $recNum = $post['recNum'];
        $smsParam = ['code'=> ''.$code];
        $smsTemplateCode = isset($post['smsTemplateCode'])?$post['smsTemplateCode']:"SMS_27400041";
        $smsType = isset($post['smsType']) ? $post['smsType'] : 'normal';
        $smsFreeSignName = isset($post['smsFreeSignName']) ? $post['smsFreeSignName'] : '游学乐园';
        $extend = isset($post['extend']) ? $post['extend'] : '';
        
        $result = TaobaoSMS::send($recNum, $smsParam,$smsTemplateCode,$smsType,$smsFreeSignName,$extend);
        if($result['success'])
        {
             //检查session是否打开  
            if(!Yii::$app->session->isActive){  
                Yii::$app->session->open();  
            }  
            //验证码和短信发送时间存储session  
            Yii::$app->session->set('phone_check_sms_code',$code);  
            Yii::$app->session->set('phone_check_sms_time',time());  
        }
        return [
            'code'=>($result['success'] ? 0 : 1),
            'msg'=>$result['msg'],
            'data'=>$result['resp'],
        ];
    }
    
    
    
    /**
     * 查找用户模型
     * @param integer $id   用户模型id
     * @return User 用户模型
     * @throws NotFoundHttpException
     */
    private function findModel($id)
    {
        if(($model = User::findOne(['id'=>$id])) !== null)
            return $model;
        else
            throw new NotFoundHttpException();
    }
}
