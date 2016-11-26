<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\wskeee\taobao;

include 'sdk/TopSdk.php';
/**
 * Description of TaobaoSMS
 *
 * @author Administrator
 */
class TaobaoSMS {
    /* appKey */
    private static $appkey = "23538420";
    /* $secretKey */
    private static $secretKey = "1045553417ff3b614e531ff1953cf346";
    
    /**
     * 发送验证码
     * @param string $recNum            短信接收号码。支持单个或多个手机号码，传入号码为11位手机号码，不能加0或+86。
     *                                   群发短信需传入多个号码，以英文逗号分隔，一次调用最多传入200个号码。
     *                                   示例：18600000000,13911111111,13322222222
     * 
     * @param string|array $smsParam    短信模板变量，传参规则{"key":"value"}，key的名字须和申请模板中的变量名一致，多个变量之间以逗号隔开。
     *                                   示例：针对模板“验证码${code}，您正在进行${product}身份验证，打死不要告诉别人哦！”，
     *                                   传参时需传入{"code":"1234","product":"alidayu"}
     * 
     * @param string $smsTemplateCode   短信模板ID，传入的模板必须是在阿里大于“管理中心-短信模板管理”中的可用模板。示例：SMS_585014
     * 
     * @param string $smsType           短信类型，传入值请填写normal
     * 
     * @param string $smsFreeSignName   短信签名，传入的短信签名必须是在阿里大于“管理中心-短信签名管理”中的可用签名。
     *                                   如“阿里大于”已在短信签名管理中通过审核，则可传入”阿里大于“（传参时去掉引号）作为短信签名。
     *                                   短信效果示例：【阿里大于】欢迎使用阿里大于服务。
     * 
     * @param string $extend            公共回传参数，在“消息返回”中会透传回该参数；举例：用户可以传入自己下级的会员ID，
     *                                   在消息返回时，该会员ID会包含在内，用户可以根据该会员ID识别是哪位会员使用了你的应用
     * @return array ['success'=>true/false,'msg'=>'xx',resp=>Object] <br/> 
     * resp <br/> 
     * 成功={             <br/>     
     *  err_code:int=>0,                 错误码<br/>
     *  model:string=>134523^4351232,    返回结果<br/>
     *  success:boolean=>true,           true表示成功，false表示失败<br/>
     *  msg:strin=>成功                  返回信息描述<br/>
     * }<br/> 
     * <br/> 
     * 失败={<br/> 
     *  'code' => int 15<br/> 
     *  'msg' => string 'Remote service error' (length=20)<br/> 
     *  'sub_code' => string 'isv.MOBILE_NUMBER_ILLEGAL' (length=25)<br/> 
     *  'sub_msg' => string '号码格式错误' (length=18)<br/> 
     *  'request_id' => string 'ish2jlalue6p' (length=12)<br/> 
     * }<br/> 
     */
    public static function send($recNum,$smsParam,$smsTemplateCode='SMS_27400041',$smsType='normal',$smsFreeSignName='游学乐园',$extend=""){
        /* @var $topClient TopClient */
        $topClient = new \TopClient(self::$appkey, self::$secretKey);
        $topClient->format = 'json';
        $req = new \AlibabaAliqinFcSmsNumSendRequest();
        $req ->setExtend( $extend );
        $req ->setSmsType( $smsType );
        $req ->setSmsFreeSignName( $smsFreeSignName );
        $req ->setSmsParam( is_array($smsParam) ? json_encode($smsParam) : $smsParam );
        $req ->setRecNum( $recNum );
        $req ->setSmsTemplateCode( $smsTemplateCode );
        $resp = $topClient ->execute( $req );
        $result = ['resp'=>$resp] ;
        if(isset($resp->result))
        {
            $result['success']=true;
        }else{
            $result['success']=false;
            $result['msg']=$resp->sub_msg;
        }
        return $result;
    }
}
