<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace frontend\controllers;

use maxwen\easywechat\Wechat;
use Yii;
use yii\web\Controller;

/**
 * Description of WCAuthController
 *
 * @author Administrator
 */
class WCAuthController extends Controller{
    public function __construct($id, $module, $config = array()) {
        parent::__construct($id, $module, $config);
        /* @var $wechat Wechat */
        $wechat = Yii::$app->wechat;
        if (Yii::$app->wechat->isWechat && !Yii::$app->wechat->isAuthorized()) {
            return Yii::$app->wechat->authorizeRequired()->send();
        }
    }
}
