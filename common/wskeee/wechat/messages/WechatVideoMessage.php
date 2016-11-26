<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\wskeee\wechat\messages;

/**
 * Description of WechatVideoMessage
 *
 * @author Administrator
 */
class WechatVideoMessage extends WechatBaseMessage{
    public function build() {
        $object = $this->source;
        $videoArray = $this->data;
        $itemTpl = "<Video>
                        <MediaId><![CDATA[%s]]></MediaId>
                        <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                    </Video>";

        $item_str = sprintf($itemTpl, $videoArray['MediaId'], $videoArray['ThumbMediaId'], $videoArray['Title'], $videoArray['Description']);

        $xmlTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[video]]></MsgType>
                        $item_str
                    </xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
}
