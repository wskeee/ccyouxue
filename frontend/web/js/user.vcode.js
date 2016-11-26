/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function(win,$){
    var Wskeee = win.Wskeee = win.Wskeee || {};
    Wskeee.ccyx = Wskeee.ccyx || {};
    var UserVcode = function(){
        this.countdown = 0;
        this.cookie = Wskeee.ccyx.cookie;
    };
    /* 手机验证码 cookie 键值名 */
    var COOKIE_PHONE_VCODE = "cookie_phone_vcode";
    
    var p = UserVcode.prototype;
    
    //计时
    p.countdown = null;
    //计时器 ID
    p.timerID = null;
    //cookie
    p.cookie = null;
    
    /**
     * 发送验证码
     * @param {int} mobile 手机号
     * @param {DOM} 显示计时对象
     * @returns {void}
     */
    p.getVcode = function (mobile,obj) {
        //检查手机是否合法  
        var result = this.isPhoneNum(mobile);
        if (result) {
            //检查手机号码是否存在  
            var exists_result = this.checkMobileExists({"mobile": mobile});
            if (!exists_result) {
                var code = 100000 + Math.round(Math.random()*(999999-100000));
                this.sendVcode({
                    recNum: mobile,
                    smsParam: {code:code},
                    smsTemplateCode: 'SMS_27400041',
                    smsFreeSignName: '游学乐园'
                });
                this.cookie.addCookie(COOKIE_PHONE_VCODE, 60, 60);//添加cookie记录,有效时间60s  
                this.startTime(obj);//开始倒计时  
            }else
                alert('该号码已使用！');
        }
    } 
    
    /**
     * 检查手机号是否存在
     * @param {Object} queryParam   {key:value}
     * @returns {boolean}
     */
    p.checkMobileExists = function (queryParam) {
        $.ajax({
            async: false,
            cache: false,
            type: 'POST',
            url: '/user/check-mobile-exists', // 请求的action路径  
            data: queryParam,
            error: function () {// 请求失败处理函数  
                alert('请求失败！');
                return true;
            },
            success: function (result) {
                if (result.data.isExist) {
                    alert('该手机号码已使用！');
                    return true;
                }else {
                    return false;
                }
            }
        });
    }  
    
    /**
     * 发送验证码
     * @param {type} queryParam     {key:value}
     * @returns {boolean}
     */
    p.sendVcode = function (queryParam) {
        $.ajax({
            async: false,
            cache: false,
            type: 'POST',
            url: '/vcode/default/send', // 请求的action路径  
            data: queryParam,
            error: function () {// 请求失败处理函数  
                
            },
            success: function (result) {
                console.log(result);
                if (result.code == 0) {
                    alert('短信发送成功，验证码10分钟内有效,请注意查看手机短信。如果未收到短信，请在60秒后重试！');
                    return true;
                }
                else {
                    alert('短信发送失败，请和网站客服联系！');
                    return false;
                }
            }
        });
    }  
    
    /**
     * 开始计时
     * @param {显示对象 DOM} obj
     * @returns {void}
     */
    p.startTime = function (obj) {
        var _this = this;
        this.countdown = this.cookie.getCookieValue(COOKIE_PHONE_VCODE) ? this.cookie.getCookieValue(COOKIE_PHONE_VCODE) : 0;
        if (this.countdown == 0) {
            obj.removeClass("weui-btn_disabled");
            obj.html('获取验证码');
            return;
        } else {
            obj.addClass("weui-btn_disabled");
            obj.html(this.countdown + "秒后重发");
            this.countdown--;
            this.cookie.addCookie(COOKIE_PHONE_VCODE, this.countdown, this.countdown + 1);
        }
        this.timerID = setTimeout(function () {
            _this.startTime(obj)
        }, 1000) //每1000毫秒执行一次}  
    };
    
    /**
     * 取消计时器执行
     * @returns {void}
     */
    p.stopTime = function(){
        cleartimeout(this.timerID);
    }
    
    /**
     * 校验手机号是否合法  
     * @returns {Boolean}
     */
    p.isPhoneNum = function (value) {
        var phonenum = value;
        var myreg = myreg = /^(((13[0-9]{1})|(14[0-9]{1})|(17[0]{1})|(15[0-3]{1})|(15[5-9]{1})|(18[0-9]{1}))+\d{8})$/;
        if (!myreg.test(phonenum)) {
            alert('请输入有效的手机号码！');
            return false;
        } else {
            return true;
        }
    }  
    
    Wskeee.ccyx.UserVcode = UserVcode;
})(window,jQuery);

