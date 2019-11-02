/*
* 全屏
* @Author: safer
* @Date:   2017-08-01 16:48:01
* @Last Modified time: 2017-08-02 10:09:25
*/

;(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else if (typeof exports === 'object' && typeof module !== 'undefined') {
        // CommonJS. Register as a module
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function ($){
    var _bind, _bindAll, _init, _view, _delegateEvents;

    $.fn.fullscreen = function(opt){

        if (this[0] == void 0) throw new Error('Your selector dom has not find :(');
        if (!opt) opt = {};

        // 默认配置
        opt = $.extend(true,{},opt);

        opt.$el = this;
        new _bind(_init, _view()) (opt);
        return this;
    };

    _view = function(){return {

            tagName : 'a',
            className : 'dropdown-toggle',
            events : {},
            root: {},
            _icon: {
                'small': 'glyphicon glyphicon-resize-small',
                'full': 'glyphicon glyphicon-fullscreen',
            },

            initialize : function(options){
                this.options = options;
                _bindAll(this, 'changeScreenState');
                this.$el.on('click', this.changeScreenState);
                this.render();
            },

            render: function() {
                var opt = this.options;
                var tpl = '<i></i>';
                this.$el.html(tpl);
                this.$el.attr('href', '#');
                this.$el.attr('data-state', 'false');
                this.$icon = this.$el.find('i');
                this.$icon.attr('class', this._icon.full);
            },

            changeScreenState: function(e) {
                var _this  = e.currentTarget;
                var state  = _this.getAttribute('data-state');
                var docElm = document.documentElement;
                if (state == 'false') {
                    if (docElm.requestFullscreen) {
                        docElm.requestFullscreen();
                    }
                    else if (docElm.msRequestFullscreen) {
                        docElm = document.body; //overwrite the element (for IE)
                        docElm.msRequestFullscreen();
                    }
                    else if (docElm.mozRequestFullScreen) {
                        docElm.mozRequestFullScreen();
                    }
                    else if (docElm.webkitRequestFullScreen) {
                        docElm.webkitRequestFullScreen();
                    }
                    _this.setAttribute('data-state', 'true');
                    this.$icon.attr('class', this._icon.small);
                }else{
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    }
                    else if (document.msExitFullscreen) {
                        document.msExitFullscreen();
                    }
                    else if (document.mozCancelFullScreen) {
                        document.mozCancelFullScreen();
                    }
                    else if (document.webkitCancelFullScreen) {
                        document.webkitCancelFullScreen();
                    }
                    _this.setAttribute('data-state', 'false');
                    this.$icon.attr('class', this._icon.full);
                }
                return false;
            },

            videoFullscreen: function() {
                // var marioVideo = document.getElementById("mario-video")
                //     videoFullscreen = document.getElementById("video-fullscreen");

                // if (marioVideo && videoFullscreen) {
                //     videoFullscreen.addEventListener("click", function (evt) {
                //         if (marioVideo.requestFullscreen) {
                //             marioVideo.requestFullscreen();
                //         }
                //         else if (marioVideo.msRequestFullscreen) {
                //             marioVideo.msRequestFullscreen();
                //         }
                //         else if (marioVideo.mozRequestFullScreen) {
                //             marioVideo.mozRequestFullScreen();
                //         }
                //         else if (marioVideo.webkitRequestFullScreen) {
                //             marioVideo.webkitRequestFullScreen();
                //             /*
                //                 *Kept here for reference: keyboard support in full screen
                //                 * marioVideo.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
                //             */
                //         }
                //     }, false);
                // }
            }

        }
    };

    // 初始化
    _init = function(options) {
        var params, tag_name, events, element, id;
        id = options.$el[0].id;
        params   = {class: this.className ||　''};
        tag_name = this.tagName ? '<'+this.tagName+'>' : '<div>';
        element  = $(tag_name, params);
        this.$el = options.$el;
        this.$el.html(element);
        this.$el = element;
        this.el  = this.$el[0];
        this.id  = id;
        this.$ = function(selector){
            return this.$el.find(selector);
        };
        this.initialize(options);
        events = this.events || '';
        if(events){
            _bind(_delegateEvents, this) (events);
        }
        return this;
    };

    // 事件委托
    _delegateEvents = function(events){
        var method, match, eventName, selector;
        for(var key in events){
            method = events[key];
            if (typeof method != 'function') method = this[events[key]];
            if (!method) continue;
            match     = key.match(/^(\S+)\s*(.*)$/);
            eventName = match[1];
            selector  = match[2];
            method    = _bind(method, this);
            if (selector === '') {
                this.$el.on(eventName, method);
            } else {
                this.$el.on(eventName, selector, method);
            }
        }
    };

    // 多个函数绑定
    _bindAll = function(obj) {
        var i, key, length = arguments.length;
        if (length <= 1) throw new Error('_bindAll must be passed function names');
        for (i = 1; i < length; i++) {
            key = arguments[i];
            obj[key] = _bind(obj[key], obj);
        }
        return obj;
    };

    // 函数绑定
    _bind = function(fn, context){
        return function(){
            return fn.apply(context, arguments);
        }
    };
}));
