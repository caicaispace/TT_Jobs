/*
* 通用弹窗通知组件
* @Author: 0ivi0
* @Date:   2017-05-22 16:24:55
* @Last Modified by:   0ivi0
* @Last Modified time: 2017-05-25 14:40:36
*/
;(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['/tpls/default/plugins/toastr/toastr.js'], factory);
    } else if (typeof exports === 'object' && typeof module !== 'undefined') {
        // CommonJS. Register as a module
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function (toastr){
    var type, title, msg, time;
    var func = function(params){
        if (typeof params != 'object') {
            type = arguments[0];
            msg  = arguments[1];
        }else{
            type  = params.type || '';
            title = params.title || '';
            msg   = params.msg || '';
            time  = params.time || '';
        }
        toastr.options = {
            newestOnTop: true,
        };
        return toastr[type](msg);
    }
    return func;
}));
