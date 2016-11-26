<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\wskeee\wechat\messages;

/**
 * Description of WechatBaseMessage
 *
 * @author Administrator
 */
class WechatBaseMessage {
    
    //消息源 {Obejct}
    protected $source;
    
    //回复数据
    protected $data = null;
    
    /**
     * 
     * @param type $source      消息源
     * @param type $data        回复数据
     */
    public function __construct($source,$data = null) {
        $this->source = $source;
        $this->data = $data;
    }
    
    /**
     * 合成信息字符串
     * @return string   
     */
    public function build(){
        return '';
    }
}
