<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\wskeee\easywechat\actions;

use common\models\User;
use common\models\UserProfile;
use EasyWeChat\Message\Text;
use Yii;

/**
 * Description of SubscribeAction
 *
 * @author Administrator
 */
class SubscribeAction extends BaseAction{
    public function run() {
        //创建对应用户
        $openid = $this->message->FromUserName;
        $user = User::findOne(['openid' => $openid]);
        if($user == null){
            $user = new User();
        }
        $user_source = $this->userServer->get($openid);
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $user->setScenario(User::SCENARIO_CREATE_WECHAT);
            $user->openid = $user_source['openid'];
            $user->unionid = $user_source['unionid'];
            $user->username = $user_source['openid'];
            $user->nickname = $user_source['nickname'];
            $user->subscribe = $user_source['subscribe'];
            $user->sex = $user_source['sex'];
            $user->avatar = $user_source['headimgurl'];
            $user->subscribe_time = $user_source['subscribe_time'];
            $user->groupid = $user_source['groupid'];
            $user->status = User::STATUS_ACTIVE;
            
            if($user->save()){
                /* @var $profile UserProfile */
                $profile = UserProfile::findOne(['u_id'=>$user->id]);
                if($profile == null)$profile = new UserProfile();
                $profile->u_id = $user->id;
                $profile->country = $user_source['country'];
                $profile->province = $user_source['province'];
                $profile->city = $user_source['city'];
                $profile->subscribe_scene_id = (!empty($this->message->EventKey))?(str_replace("qrscene_","",$this->message->EventKey)):-1;
                $profile->language = $user_source['language'];
                $profile->remark = $user_source['remark'];
                
                if($profile->save()){
                    
                    $transaction->commit();
                    
                    $content = "欢迎来到 游学乐园!";
                    $scanContent = (new ScanAction($this->message))->run()->content;
                    $content .= $scanContent == "" ? "" : "\n$scanContent";
                    
                    return new Text(['content' => $content]);
                }else{
                    Yii::error("profile保存失败，原因:{$profile->getFirstErrors()}");
                }
            }
            // ... executing other SQL statements ...
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error("注册用户失败，原因:{$e->getTraceAsString()}");
            return new Text(['content' => "注册用户失败，原因:{$e->getMessage()}"]);
        }
    }
}
