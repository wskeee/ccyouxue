<?php
namespace common\wskeee\wechat\messages;

use common\wskeee\wechat\messages\WechatBaseMessage;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of WechatTextMessage
 *
 * @author Administrator
 */
class WechatTextMessage extends WechatBaseMessage{
    
    public function build(){
        $content = $this->data;
        if (!isset($content) || empty($content)){
            return "";
        }
        $xmlTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[text]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                    </xml>";
        $result = sprintf($xmlTpl,$this->source->FromUserName, $this->source->ToUserName, time(), $content);

        return $result;
    }
}
