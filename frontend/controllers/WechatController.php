<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace frontend\controllers;

use common\wskeee\easywechat\EasyWechatResponse;
use EasyWeChat\Foundation\Application;
use maxwen\easywechat\Wechat;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * Description of WechatController
 *
 * @author Administrator
 */
class WechatController extends Controller{
    
    public $enableCsrfValidation = false;
    
    /**
     * 所有微信入口
     */
    public function actionEasywechat() {
        /* @var $wechat Wechat */
        $wechat = Yii::$app->wechat;
        /* @var $app Application */
        $app = $wechat->app;
        $server = $app->server;
        $user = $app->user;

        $server->setMessageHandler(function($message) use ($user) {
            /* @var $response EasyWechatResponse */
            $response = new EasyWechatResponse();
            return $response->responseMsg($message);
        });
        Yii::trace('access_token: '.$app['access_token']->getToken());
        $server->serve()->send();
    }
    
    /**
     * 网页认证回调，授权后带code调用这里
     */
    public function actionOauthCallback(){
        /* @var $wechat Wechat */
        $wechat = Yii::$app->wechat;
        /* @var $app Application */
        $app = $wechat->app;
        $oauth = $app->oauth;
        // 获取 OAuth 授权结果用户信息
        $user = $oauth->user();
        $wechat->authorize($user);
    }
}
