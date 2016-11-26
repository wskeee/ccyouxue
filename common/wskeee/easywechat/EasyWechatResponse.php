<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\wskeee\easywechat;

use common\wskeee\easywechat\actions\ActionFactory;
use EasyWeChat\Message\AbstractMessage;

/**
 * Description of EasyWechatResponse
 *
 * @author Administrator
 */
class EasyWechatResponse {
    /**
     * 响应消息
     * @param AbstractMessage $message   公众服务器传过来的数据
     * @return string
     */
    public function responseMsg($message)
    {
        $result = '';
        switch ($message->MsgType) {
            case 'event':
                # 事件消息...
                $result = ActionFactory::receiveEvent($message);
                break;
            case 'text':
                # 文字消息...
                break;
            case 'image':
                # 图片消息...
                break;
            case 'voice':
                # 语音消息...
                break;
            case 'video':
                # 视频消息...
                break;
            case 'location':
                # 坐标消息...
                break;
            case 'link':
                # 链接消息...
                break;
            // ... 其它消息
            default:
                # code...
                break;
        }
        return $result;
    }

    //接收文本消息
    /**
     * 
     * @param @param AbstractMessage $message
     * @return string 
     */
    private function receiveText($message)
    {
        $message = null;
        $keyword = trim($message->Content);
    }
}
