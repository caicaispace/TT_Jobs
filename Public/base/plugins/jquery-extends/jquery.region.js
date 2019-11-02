/**
 * 地区联动
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

    $.fn.region = function(opt){

        if (this[0] === void 0) throw new Error('Your selector dom has not find :(');
        if (!opt) opt = {};

        // 默认配置
        opt = $.extend(true,{
            url: '/api/region',
            level: 3,
            must: false,
            must_identify: 'data-validation="must"',
            default_region: '',
            callback: '',
            first_callback: '',
            end_callback: '',
            level_hash: {
                0: {
                    select_name: 'country_code',
                    select_title: '--国家--'
                },
                1: {
                    select_name : 'province_code',
                    select_title: '--省/直辖市--'
                },
                2: {
                    select_name : 'city_code',
                    select_title: '--城市--'
                },
                3: {
                    select_name : 'district_code',
                    select_title: '--县/区--'
                }
            }
        },opt);

        opt.$el = this;
        new _bind(_init, _view()) (opt);
        return this;
    };

    _view = function(){return {

            tagName : 'div',
            className : 'jquery-region btn-group',
            events : {
                'change .select-region' : 'change'
            },
            root: {
                regions: {},
                current_region: {name:'',value:''},
                selected_region: [],
                count: 1, // 点击次数
                index: 0, // 当前 select 索引
            },

            initialize : function(options){
                this.options = options;
                _bindAll(this, 'init');
                $.get(options.url, this.init, 'json');
            },

            init: function(result){
                this.res = result;
                this.render();
            },

            render: function() {

                var opt          = this.options;
                var list         = this.res.list;
                var default_region = opt.default_region;

                var select = this.getSelectTpl(list);
                this.$el.html(select);

                // 无初始值并且首次加载执行完将执行次数改为调用级别
                if(default_region == void 0 && this.root.count <= opt.level){
                    this.root.count = opt.level;
                }
                // 有初始值即执行子地区查询
                if (default_region && default_region.province_code != '' && this.root.index == 0){
                    this.$('[name="province_code"]').val(default_region.province_code).change();
                }else{
                    this.options.default_region = ''; // 初始值出错清空初始值
                }

            },

            change: function(e){
                var $this = $(e.currentTarget);
                // this.root.current_region.name = $this.find('option:selected').html();
                // this.root.current_region.value = $this.val();
                if ($this.val() > -1){
                    this.changed($this);
                }else{
                    this.root.regions = [{value:-1, name:'all'}];
                }
                $this.nextAll().remove();
            },

            changed: function($this) {

                var level = this.options.level;
                var value = $this[0].value || -1;
                var name  = $this.find('option:selected').html() || '';
                var index = $this.index() || 0;

                this.root.current_region = {
                    name: name,
                    value: value
                };

                var length = index + 1 <= level ? index + 1 : level;
                this.root.selected_region[ index ] = name;
                this.root.selected_region.length   = length;
                this.root.regions[index] = {
                    value: value,
                    name: name
                };
                this.root.index = index+1;
                if (index + 1 < level) {
                    if (!isNaN(value) && value > 0) {
                        var func = _bind(function(result){
                            this.setNext(result, $this);
                        }, this);
                        $.get(this.options.url+'?code=' + value, func, 'json');
                    }
                }
                this._callback();

            },

            setNext: function(result, $this) {

                var list = result['list'];
                if (list != null && list.length > 0) {

                    var $next = $(this.getSelectTpl(list));
                    $next.insertAfter($this);

                    var level = this.options.level_hash;
                    var select_name  = level[ this.root.index+1 ]['select_name'];
                    var default_region = this.options.default_region;
                    if (default_region && (default_region.province_code == $this.val() || default_region.city_code == $this.val())){
                        $next.val(default_region[select_name]).change();
                    }
                }
            },

            getSelectTpl: function(list){

                var hashs = this.options.level_hash[this.root.index+1];
                var select_title   = hashs['select_title'];
                var select_name    = hashs['select_name'];
                var must_identify  = this.options.must ? this.options.must_identify : '';
                var select_options = '<option value="-1" selected >'+select_title+'</option>'
                var len = list.length;
                var i   = 0;
                while (len > i ) {
                    select_options += '<option value="' + list[i].code + '">' + list[i].name + '</option>';
                     i += 1;
                }
                var select = '<select name="'+select_name+'" class="select-region form-control" style="margin-right:5px" '+must_identify+'>'
                                +select_options+
                              '</select>';
                return select;
            },

            _callback: function() {
                var callback       = this.options.callback;
                var first_callback = this.options.first_callback;
                // var end_callback   = this.options.end_callback;
                var default_region   = this.options.default_region;
                var level          = this.options.level;
                var count          = this.root.count;
                this.root.count    += 1;
                /* 无初始值 */
                if (default_region == '' || count > level){
                    if (typeof callback == 'function') callback(this.root);
                    return;
                }
                /* 有初始值 */
                if (default_region != '' && count == 1){  // 首次执行
                    if (typeof first_callback === 'function') first_callback(this.root);
                    return;
                } else if(count == level) { // 最后一次执行
                    this.options.default_region = '';
                    // if (typeof end_callback == 'function') end_callback(this.root);
                    if (typeof callback === 'function') callback(this.root);
                }
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
            if (typeof method !== 'function') method = this[events[key]];
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
