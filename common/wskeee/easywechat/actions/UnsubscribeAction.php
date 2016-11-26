<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\wskeee\easywechat\actions;

use common\models\User;

/**
 * Description of UnsubscribeAction
 *
 * @author Administrator
 */
class UnsubscribeAction extends BaseAction{
    public function run(){
        //创建对应用户
        $openid = $this->message->FromUserName;
        /* @var $user User */
        $user = User::findOne(['openid' => $openid]);
        if($user != null){
            $user->status = User::STATUS_DELETED;
            if($user->save()){
                \Yii::trace("用户取消关注成功：openid={$openid}");
            }else{
                \Yii::error("用户取消关注失败：openid={$openid}");
            }
        }
        return '';
    }
}
