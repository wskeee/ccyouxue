<?php

use frontend\assets\UserUpdateAsset;
use yii\web\View;
use yii\widgets\ActiveForm;
/* @var $this View */
?>
<div class="user-update">
    <div class="weui-cells__title">更新信息</div>
    <?php $form = ActiveForm::begin([
        
    ]); ?>
        <div class="weui-cells weui_cells_form">
            <div class="weui-cell weui-cell_warn">
                <div class="weui-cell__hd">
                    <label class="weui-label"><?= Yii::t('ccyx/user-profile', 'phone') ?></label>
                </div>
                <div class="weui-cell__bd weui-cell_primary">
                    <input id="phone" name="User[phone]" class="weui-input" type="tel" value="<?= $model->phone ?>" placeholder="<?= Yii::t('ccyx/user-profile','phone-holder') ?>"/>
                </div>
                <div class="weui-cell__ft">
                    <i class="weui-icon-warn"></i>
                </div>
            </div>
            <div class="weui-cell weui-cell_vcode">
                <div class="weui-cell__hd">
                    <label class="weui-label"><?= Yii::t('ccyx/user-profile','vcode') ?></label>
                </div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="tel" placeholder="<?= Yii::t('ccyx/user-profile','vcode-holder') ?>">
                </div>
                <div class="weui-cell__ft">
                    <a id="vcode-btn" href="javascript:getVcode();" class="weui-vcode-btn weui-btn weui-btn_default"><?= Yii::t('ccyx/user-profile','get-vcode') ?></a>
                </div>
            </div>
        </div>
        <input type="submit" class="weui-btn weui-btn_primary" value="<?= Yii::t('ccyx/user-profile','submit') ?>"/>
    <?php $form->end(); ?>
</div>
<?php
    $this->registerCssFile('http://res.wx.qq.com/open/libs/weui/1.0.2/weui.min.css');
    UserUpdateAsset::register($this);
    
    $js = <<<JS
        var vcode;
        function onReady(){
            vcode = new Wskeee.ccyx.UserVcode();
            vcode.startTime($('#vcode-btn'));
        }
        function getVcode(dom){
            if($('#vcode-btn').hasClass('weui-btn_disabled'))
                return;
            vcode.getVcode($('#phone').val(),$('#vcode-btn'));
        }
        window.onload = onReady;
JS;
    $this->registerJs($js,  View::POS_HEAD); 
?>