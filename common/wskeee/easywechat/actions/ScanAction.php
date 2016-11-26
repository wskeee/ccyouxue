<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\wskeee\easywechat\actions;

use common\models\games\GameLoginHistory;
use common\models\games\GameLoginQrcode;
use common\models\games\Scene;
use common\models\User;
use EasyWeChat\Message\Text;
use Yii;

/**
 * Description of ScanAction
 *
 * @author Administrator
 */
class ScanAction extends BaseAction{
    public function run() {
        //创建对应用户
        $openid = $this->message->FromUserName;
        $eventKey = (!empty($this->message->EventKey))?(str_replace("qrscene_","",$this->message->EventKey)):-1;
        /* @var $user User */
        $user = User::findOne(['openid' => $openid]);
        $content='';
        if(((int)$eventKey) >= 10000)
        {
            $gameRcode = GameLoginQrcode::findOne(['id'=>$eventKey]);
            if($gameRcode)
            {
                //添加游戏登录记录
                $gameLoginHistory = new GameLoginHistory();
                $gameLoginHistory->scene_id = $gameRcode->scene_id;
                $gameLoginHistory->game_id = $gameRcode->game_id;
                $gameLoginHistory->u_id = $user->id;
                
                if($gameLoginHistory->save()){
                    $scene = Scene::findOne(['id'=>$gameRcode->scene_id]);
                    Yii::trace("游戏登录成功！sceneId: {$scene->id} sceneName: {$scene->name} openId:{$user->openid} name:{$user->nickname}",'ccyouxue');
                    $content = "游戏登录成功！";
                }
            }
        }else if((int)$eventKey > 0){
            $content = '欢迎回来！';
        }else if(is_string($eventKey)){
            $content = '扫描场景：'.$eventKey;
        }
        return new Text(['content'=>$content]);
    }
}
