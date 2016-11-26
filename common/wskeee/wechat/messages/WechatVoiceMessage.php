<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\wskeee\wechat\messages;

/**
 * Description of WechatVoiceMessage
 *
 * @author Administrator
 */
class WechatVoiceMessage extends WechatBaseMessage{
    public function build() {
        $object = $this->source;
        $voiceArray = $this->data;
        $itemTpl = "<Voice>
                        <MediaId><![CDATA[%s]]></MediaId>
                    </Voice>";

        $item_str = sprintf($itemTpl, $voiceArray['MediaId']);
        $xmlTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[voice]]></MsgType>
                    $item_str
                </xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
}
