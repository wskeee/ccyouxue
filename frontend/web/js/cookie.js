/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function(win,$){
    var Wskeee = win.Wskeee = win.Wskeee || {};
    Wskeee.ccyx = Wskeee.ccyx || {};
    var Cookie = function(){
        
    };
    var p = Cookie.prototype;
    
    /**
     * 添加 cookie
     * @param {string} name     cookie 名
     * @param {*} value         cookie 值
     * @param {number} expiresTime  到期时长 单位秒     
     * @returns {void}  
     */
    p.addCookie = function (name, value, expiresTime) {
        var cookieString = name + "=" + escape(value);
        //判断是否设置过期时间,0代表关闭浏览器时失效  
        if (expiresTime > 0) {
            var date = new Date();
            date.setTime(date.getTime() + expiresTime * 1000);
            cookieString = cookieString + ";expires=" + date.toUTCString();
        }
        document.cookie = cookieString;
    } 
    
    /**
     * 获取 cookie
     * @param {string} name
     * @returns {Object}
     */
    p.getCookieValue = function (name) {
        var strCookie = document.cookie;
        var arrCookie = strCookie.split("; ");
        for (var i = 0; i < arrCookie.length; i++) {
            var arr = arrCookie[i].split("=");
            if (arr[0] == name) {
                return unescape(arr[1]);
                break;
            }
        }
        return null;
    } 
    
    Wskeee.ccyx.cookie = new Cookie();
})(window,jQuery);

