<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\wskeee\easywechat\actions;

use EasyWeChat\Foundation\Application;
use EasyWeChat\Message\AbstractMessage;
use EasyWeChat\Server\Guard;
use EasyWeChat\User\User;
use maxwen\easywechat\Wechat;
use Yii;

/**
 * Description of BaseAction
 *
 * @author Administrator
 */
class BaseAction {
    /**
     * 
     * @param AbstractMessage $message
     */
    protected $message;
    /* @var $wechat Wechat  */
    protected $wechat;
    /* @var $app Application */
    protected $app;
    /* @var $server Guard*/
    protected $server;
    /* @var $user User*/
    protected $userServer;
    
    public function __construct($message) {
        $this->message = $message;
        $this->wechat = Yii::$app->get('wechat');
        $this->app = $this->wechat->app;
        $this->server = $this->app->server;
        $this->userServer = $this->app->user;
    }
    
    /**
     * 执行动作返回结果
     * @return string 
     */
    public function run(){
        return '';
    }
}
