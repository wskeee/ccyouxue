<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\wskeee\easywechat\actions;

use EasyWeChat\Message\AbstractMessage;
use EasyWeChat\Message\Text;
use Yii;

/**
 * Description of ActionFactory
 *
 * @author Administrator
 */
class ActionFactory {
    /**
     * 接收事件消息
     * @param AbstractMessage $message
     * @return string
     */
    static public function receiveEvent($message)
    {
        Yii::trace("receiveEvent： $message->Event");
        /* @var $action BaseAction */
        $action = null;
        switch ($message->Event)
        {
            case "subscribe":
                $action = new SubscribeAction($message);
                break;
            case "unsubscribe":
                $action = new UnsubscribeAction($message);
                break;
            case "CLICK":
                switch ($message->EventKey)
                {
                    case "COMPANY":
                        $content = array();
                        $content[] = array("Title"=>"方倍工作室", "Description"=>"", "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
                        break;
                    default:
                        $content = "点击菜单：".$message->EventKey;
                        return new Text(['content' => $content]);
                        break;
                }
                break;
            case "VIEW":
                $content = "跳转链接 ".$message->EventKey;
                break;
            case "SCAN":
                $action = new ScanAction($message);
                break;
            case "LOCATION":
                $content = "上传位置：纬度 ".$message->Latitude.";经度 ".$message->Longitude;
                break;
            case "scancode_waitmsg":
                if ($message->ScanCodeInfo->ScanType == "qrcode"){
                    $content = "扫码带提示：类型 二维码 结果：".$message->ScanCodeInfo->ScanResult;
                }else if ($message->ScanCodeInfo->ScanType == "barcode"){
                    $codeinfo = explode(",",strval($message->ScanCodeInfo->ScanResult));
                    $codeValue = $codeinfo[1];
                    $content = "扫码带提示：类型 条形码 结果：".$codeValue;
                }else{
                    $content = "扫码带提示：类型 ".$message->ScanCodeInfo->ScanType." 结果：".$message->ScanCodeInfo->ScanResult;
                }
                break;
            case "scancode_push":
                $content = "扫码推事件";
                break;
            case "pic_sysphoto":
                $content = "系统拍照";
                break;
            case "pic_weixin":
                $content = "相册发图：数量 ".$message->SendPicsInfo->Count;
                break;
            case "pic_photo_or_album":
                $content = "拍照或者相册：数量 ".$message->SendPicsInfo->Count;
                break;
            case "location_select":
                $content = "发送位置：标签 ".$message->SendLocationInfo->Label;
                break;
            default:
                $content = "receive a new event: ".$message->Event;
                return new Text(['content' => $content]);
                break;
        }
        return $action == null ? '' : $action->run();
    }
}
