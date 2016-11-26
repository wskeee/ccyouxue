<?php

namespace frontend\modules\vcode\controllers;

use common\wskeee\taobao\TaobaoSMS;
use Yii;
use yii\web\Controller;

/**
 * Default controller for the `vcode` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
    
    /**
     * 发送验证码
     */
    public function actionSend(){
        Yii::$app->getResponse()->format = 'json';
        $post = Yii::$app->getRequest()->getQueryParams();
        $post = array_merge($post, Yii::$app->getRequest()->getBodyParams());
        $recNum = $post['recNum'];
        $smsParam = isset($post['smsParam']) ? $post['smsParam'] : "";
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
            Yii::$app->session->set('login_sms_code',$code);  
            Yii::$app->session->set('login_sms_time',time());  
        }
        return [
            'code'=>($result['success'] ? 0 : 1),
            'msg'=>$result['msg'],
            'data'=>$result['resp'],
        ];
    }
}
