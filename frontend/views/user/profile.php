<?php

use common\models\User;
use yii\helpers\Url;
use yii\web\View;
/* @var $this View */
/* @var $user User */
?>
<h1>个人中心</h1>
<p>
    <div class="user-profile">
        <div class="weui-cells">
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <label><?= Yii::t('ccyx/user-profile', 'avatar') ?></label>
                </div>
                <div class="weui-cell__ft"><img  src="<?= $user->avatar ?>" style="width: 52px;height: 52px"/></div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <label><?= Yii::t('ccyx/user-profile', 'nickname') ?></label>
                </div>
                <div class="weui-cell__ft"><?= $user->nickname ?></div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <label><?= Yii::t('ccyx/user-profile', 'phone') ?></label>
                </div>
                <div class="weui-cell__ft">
                    <?php 
                        if(empty($user->phone))
                            echo  Yii::t('ccyx/user-profile', 'unset');
                        else
                            echo $user->phone." ";
                    ?>
                    <a href="<?= Url::to(['update','id'=>$user->id])  ?>" class="weui-btn weui-btn_mini weui-btn_primary"><?= Yii::t('ccyx/user-profile', 'modify') ?></a>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <label><?= Yii::t('ccyx/user-profile', 'email') ?></label>
                </div>
                <div class="weui-cell__ft"><?= $user->email == "" ? Yii::t('ccyx/user-profile', 'unset') : $user->nickname ?></div>
            </div>
        </div>
    </div>
<?php
    $this->registerCssFile('http://res.wx.qq.com/open/libs/weui/1.0.2/weui.min.css');
?>